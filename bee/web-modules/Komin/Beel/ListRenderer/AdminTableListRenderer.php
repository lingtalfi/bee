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

use Bee\Bat\ArrayTool;
use Bee\Bat\HtmlTool;
use Bee\Bat\StringTool;


/**
 * AdminTableListRenderer
 * @author Lingtalfi
 * 2015-01-09
 *
 * - checkboxes column on the left to select one or more lines
 * - buttons system, to create consistent buttons/buttons lines like edit, delete, print
 *
 * All this is static.
 * The admin table has no behaviour until a js layer is applied to it.
 *
 */
class AdminTableListRenderer extends TableListRenderer
{


    public function __construct($itemsRenderer = null, array $options = [])
    {
        if (!array_key_exists('buttonSep', $options)) {
            $options['buttonSep'] = ' | ';
        }
        if (!array_key_exists('buttonTag', $options)) {
            $options['buttonTag'] = 'button';
        }
        /**
         * @param buttonAttr : array|callback
         *
         *                              array callback ( buttonName )
         *
         *
         */
        if (!array_key_exists('buttonAttr', $options)) {
            $options['buttonAttr'] = [
                'class' => 'button',
            ];
        }


        parent::__construct($itemsRenderer, $options);
        if (!array_key_exists('_checkboxes', $this->options['headerColsContent'])) {
            $this->options['headerColsContent']['_checkboxes'] = '<input type="checkbox" name="allrow" />';
        }

    }


    /**
     * @param array $buttons array, each entry being one of: 
     *                              - button text
     *                                  or
     *                              - name => button text
     *
     *
     *
     */
    public function setButtonsColumn($columnName, $pos = 'last', array $buttons)
    {
        $s = '';


        $tag = $this->options['buttonTag'];

        $c = false;
        foreach ($buttons as $name => $text) {

            if (is_numeric($name)) {
                $name = $text;
            }

            $attr = $this->options['buttonAttr'];
            if (is_callable($attr)) {
                $attr = call_user_func($attr, $name);
            }
            if ('a' === $tag && !array_key_exists('href', $attr)) {
                $attr['href'] = '#';
            }

            if (true === $c) {
                $s .= $this->options['buttonSep'];
            }
            else {
                $c = true;
            }
            $s .= '<' . $tag . HtmlTool::toAttributesString($attr) . '>' . $text . '</' . $tag . '>';
        }
        $this->setSpecialColumn($columnName, $pos, $s);
    }


    protected function getHeaderLineOpening()
    {
        $th = $this->options['headerColsTag'];
        return '<' . $th . '><input type="checkbox" name="row"  /></' . $th . '>';
    }


    protected function getLineOpening()
    {
        return '<td><input type="checkbox" name="row"  /></td>';
    }


}
