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
 * RecursiveListRenderer
 * @author Lingtalfi
 * 2015-01-08
 *
 */
class RecursiveListRenderer extends ArrayListRenderer
{
    function __construct(array $options = [], $itemRenderer = null)
    {
        if (!array_key_exists('openItemChar', $options)) {
            $options['openItemChar'] = function ($value, $key, $level) {
                return '<li>';
            };
        }
        parent::__construct($options, $itemRenderer);
    }


    protected function doRender($item, $i, $level = 0)
    {
        $s = '';
        if (-1 === $this->options['levelMax'] || $level < $this->options['levelMax']) {
            $s .= $this->getChar('openItemChar', $item, $i, $level) . PHP_EOL;
            $s .= $this->getChar('getItem', $item, $i, $level);
            if (array_key_exists('children', $item)) {
                $item = $item['children'];
                $s .= $this->getChar('openListChar', $item, $i, $level) . PHP_EOL;
                $level++;
                foreach ($item as $k => $v) {
                    $s .= $this->doRender($v, $k, $level);
                }
                $level--;
                $s .= $this->getChar('closeListChar', $item, $i, $level) . PHP_EOL;
            }

            $s .= $this->getChar('closeItemChar', $item, $i, $level) . PHP_EOL;
        }
        return $s;
    }

}
