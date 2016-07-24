<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beel\ListRenderer;

use Bee\Bat\HtmlTool;


/**
 * TableListRenderer
 * @author Lingtalfi
 * 2015-01-08
 *
 *
 * We distinguish two types of columns:
 * - regular columns
 * - special columns
 *
 * We can think of regular columns as fields of a mysql row,
 * and special columns as action columns (edit button, delete button, ...).
 *
 *
 */
class TableListRenderer extends ListRenderer
{

    /**
     * array of name => column
     *
     * With column: array
     *                  The array is empty for regular columns.
     *                  For special columns, it has the following properties:
     *
     *                  - pos: the index.
     *                              When rendering,
     *                              if a special column matches the current index it is displayed,
     *                              then the regular column is always displayed since the process
     *                              loops the regular columns.
     *                              This means that special columns might be displayed on top of (before) the
     *                              regular columns.
     *
     *                  - content: string|callback ( item )
     *                              Note: if the special columns' position forces the column to be displayed
     *                              AFTER ALL regular columns, then the values from the last item will be used.
     *
     *
     *
     */
    protected $columns;

    /**
     * Filters act on the content of a column.
     * Array of colName => filter|filter[]
     *          With filter:
     *                      string callback ( value, row )
     */
    protected $filters;
    protected $options;


    protected $orderedCols;


    public function __construct($itemsRenderer = null, array $options = [])
    {
        $this->options = array_replace([
            /**
             * Display the header, only if column names are set
             */
            'useHeader' => true,
            'tableAttr' => [
                'class' => 'beeltable'
            ],
            'headerRowAttr' => [],
            'headerColsTag' => 'th',
            /**
             * array of $colName => $colAttr
             *
             * with $colAttr: null|callback|array of attr,
             *              the callback returns an array of attributes and has the following signature:
             *              array callback ( colName, index )
             *                                          first index is 0.
             */
            'headerColsAttr' => [],
            /**
             * array of $colName => $colContent
             *
             * with $colContent: callback|string,
             *              the callback returns the content and has the following signature:
             *              array callback ( colName, index )
             *                                          first index is 0.
             */
            'headerColsContent' => [],
            /**
             * array|array callback( item, i )
             */
            'lineAttr' => [],
            /**
             * array|array callback( colName, colValue, row )
             */
            'colAttr' => [],
            'useOddEven' => true,
        ], $options);
        $this->filters = [];

        if (null === $itemsRenderer) {
            $itemsRenderer = [$this, 'renderItem'];
        }

        parent::__construct($itemsRenderer);
    }


    public function render(array $items)
    {
        $this->orderedCols = null;
        $s = '';
        $s .= '<table' . HtmlTool::toAttributesString($this->options['tableAttr']) . '>';
        if (true === $this->options['useHeader'] && $this->columns) {
            $s .= $this->getHeader();
        }
        $s .= $this->getBody($items);

        $s .= '</table>';
        return $s;
    }


    public function setRegularColumns(array $columns)
    {
        foreach ($columns as $name) {
            $this->columns[$name] = [];
        }
        return $this;
    }


    public function setSpecialColumn($name, $pos = 'last', $content)
    {
        $this->columns[$name] = [
            'pos' => $pos,
            'content' => $content,
        ];
        return $this;
    }

    public function setOption($k, $v)
    {
        $this->options[$k] = $v;
        return $this;
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function setFilter($colName, $filter)
    {
        $this->filters[$colName] = $filter;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getBody(array $items)
    {
        $s = '';
        foreach ($items as $i => $item) {
            $s .= call_user_func($this->itemRenderer, $item, $i);
        }
        return $s;
    }


    private function getOrderedCols()
    {
        if (null === $this->orderedCols) {
            $this->orderedCols = [];

            // first adding regular columns
            foreach ($this->columns as $n => $info) {
                if (!array_key_exists('pos', $info)) {
                    $info['name'] = $n;
                    $info['special'] = false;
                    $this->orderedCols[] = $info;
                }
            }

            // now adding special columns
            foreach ($this->columns as $n => $info) {
                if (array_key_exists('pos', $info)) {
                    $info['name'] = $n;
                    $info['special'] = true;
                    if ('last' === $info['pos']) {
                        $this->orderedCols[] = $info;
                    }
                    else {
                        array_splice($this->orderedCols, $info['pos'], 0, [$info]);
                    }
                }
            }
        }
        return $this->orderedCols;
    }


    protected function getHeader()
    {
        $s = '';
        $s .= '<tr' . HtmlTool::toAttributesString($this->options['headerRowAttr']) . '>';
        $s .= $this->getHeaderLineOpening();


        $th = $this->options['headerColsTag'];
        $columns = $this->getOrderedCols();
        foreach ($columns as $info) {
            $name = $info['name'];
            $special = $info['special'];
            $attr = '';
            if (array_key_exists($name, $this->options['headerColsAttr'])) {
                if (is_callable($this->options['headerColsAttr'][$name])) {
                    $attr = call_user_func($this->options['headerColsAttr'][$name], $name);
                }
            }
            if (is_array($attr)) {
                $attr = HtmlTool::toAttributesString($attr);
            }

            $content = $name;
            if (array_key_exists($name, $this->options['headerColsContent'])) {
                if (is_callable($this->options['headerColsContent'][$name])) {
                    $content = call_user_func($this->options['headerColsContent'][$name], $content);
                }
                else {
                    $content = $this->options['headerColsContent'][$name];
                }
            }
            $this->filterHeaderContent($content, $name, $special);

            $s .= '<' . $th . $attr . '>' . $content . '</' . $th . '>';
        }
        $s .= '</tr>';
        return $s;
    }


    protected function filterHeaderContent(&$content, $colName, $isSpecial)
    {
    }

    protected function renderItem(array $item, $i)
    {
        $trAttr = $this->options['lineAttr'];
        if (is_callable($trAttr)) {
            $trAttr = call_user_func($trAttr, $item, $i);
        }

        if (true === $this->options['useOddEven']) {
            $class = (0 === $i % 2) ? 'even' : 'odd';
            if (array_key_exists('class', $trAttr)) {
                $trAttr['class'] .= ' ' . $class;
            }
            else {
                $trAttr['class'] = $class;
            }
        }


        $s = '';
        $s .= '<tr' . HtmlTool::toAttributesString($trAttr) . '>';
        if (null !== $c = $this->getLineOpening()) {
            $s .= $c;
        }

        // preparing positioned columns
        $posCols = $this->getOrderedCols();

        foreach ($posCols as $info) {
            $colValue = null;
            $colName = $info['name'];

            // col tag attr
            if (false === $info['special']) {
                $colValue = $item[$colName];
            }
            else {
                $colValue = $info['content'];
                if (is_callable($colValue)) {
                    $colValue = call_user_func($colValue, $item);
                }
            }

            $colAttr = $this->getColAttr($item, $colName, $colValue);
            $tag = '<td' . HtmlTool::toAttributesString($colAttr) . '>';
            $s .= $tag . $this->filterCol($colValue, $colName, $item) . '</td>';
        }

        $s .= '</tr>';
        return $s;
    }


    protected function getHeaderLineOpening()
    {
        return null;
    }

    protected function getLineOpening()
    {
        return null;
    }

    private function filterCol($content, $colName, $item)
    {
        if (array_key_exists($colName, $this->filters)) {
            $filters = $this->filters[$colName];
            if (!is_array($filters)) {
                $filters = [$filters];
            }
            foreach ($filters as $filter) {
                $content = call_user_func($filter, $content, $item);
            }
        }
        return $content;
    }

    /**
     * @param null $colValue , only set for regular column, is null for special columns
     */
    private function getColAttr($item, $colName, $colValue = null)
    {
        $colAttr = $this->options['colAttr'];
        if (is_callable($colAttr)) {
            $colAttr = call_user_func($colAttr, $item, $colName, $colValue);
        }
        return $colAttr;
    }


    private function getOldestItemRenderer()
    {
        return function (array $item, $i) {
            $class = (0 === $i % 2) ? 'even' : 'odd';
            $s = '';
            $s .= '<tr class="' . $class . '">';
            foreach ($item as $k => $v) {
                $s .= '<td>' . $v . '</td>';
            }
            $s .= '</tr>';
            return $s;
        };
    }
}
