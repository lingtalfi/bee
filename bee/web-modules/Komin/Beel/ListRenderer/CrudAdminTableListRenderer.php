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
 * CrudAdminTableListRenderer
 * @author Lingtalfi
 * 2015-01-10
 *
 * This table adds html code inside columns headers to prepare sorting.
 *
 */
class CrudAdminTableListRenderer extends AdminTableListRenderer
{


    public function __construct($itemsRenderer = null, array $options = [])
    {
        if (!array_key_exists('colSorts', $options)) {
            $options['colSorts'] = [];
        }
        parent::__construct($itemsRenderer, $options);
    }

    public function renderBody(array $items)
    {
        $this->orderedCols = null;
        return parent::getBody($items);
    }

    protected function filterHeaderContent(&$content, $colName, $isSpecial)
    {
        if (false === $isSpecial) {
            if (array_key_exists($colName, $this->options['colSorts'])) {
                $sort = $this->options['colSorts'][$colName];
            }
            else {
                $sort = 'double';
            }
            $content = '<span class="columnname">' . $content . '</span>';
            // unselectable=on is for opera:   http://stackoverflow.com/questions/69430/is-there-a-way-to-make-text-unselectable-on-an-html-page
            $content .= '<span unselectable="on" class="unselectable sorter ' . $sort . '">&nbsp;&nbsp;&nbsp;</span>';
        }
    }

    protected function getHeader()
    {
        return '<thead>' . parent::getHeader() . '</thead>';
    }

    protected function getBody(array $items)
    {
        return '<tbody>' . parent::getBody($items) . '</tbody>';
    }


}
