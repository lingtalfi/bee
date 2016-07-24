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
 * ArrayListRenderer
 * @author Lingtalfi
 * 2015-01-08
 *
 * There are three elements:
 *
 *
 * item: an item is composed of a key and a value
 * list: a list contains other items
 * rootList: the topmost list that wraps the topmost items
 *
 *
 *
 */
class ArrayListRenderer extends ListRenderer
{

    protected $options;

    function __construct(array $options = [], $itemRenderer = null)
    {
        if (null === $itemRenderer) {
            $itemRenderer = [$this, 'doRender'];
        }
        parent::__construct($itemRenderer);
        $this->options = array_replace([
            /**
             * The number of levels to display
             * -1 means infinite,
             * all other numbers represent themselves
             */
            'levelMax' => -1,
            /**
             * Each property below can be a string or a callback.
             * If it's a callback, it has the following signature:
             *              string callback ( value, key, level )
             *
             * For topmost chars (openRootListChar and closeRootListChar),
             * level, key and value are all set to null.
             *
             */
            'openRootListChar' => '<ul>',
            'closeRootListChar' => '</ul>',
            'openListChar' => '<ul>',
            'closeListChar' => '</ul>',
            'openItemChar' => '<li>$key: ', // this one can hold the key
            'closeItemChar' => '</li>',
            'getItem' => '$value', // this one holds the value
        ], $options);
    }


    public function render(array $items)
    {
        $s = '';
        if ($items) {
            $s .= $this->getChar('openRootListChar', null, null, null);
            foreach ($items as $i => $item) {
                $s .= call_user_func($this->itemRenderer, $item, $i, 0);
            }
            $s .= $this->getChar('closeRootListChar', null, null, null);
        }
        return $s;
    }

    protected function doRender($item, $i, $level = 0)
    {
        $s = '';
        if (-1 === $this->options['levelMax'] || $level < $this->options['levelMax']) {
            $s .= $this->getChar('openItemChar', $item, $i, $level) . PHP_EOL;
            if (is_array($item)) {
                $s .= $this->getChar('openListChar', $item, $i, $level) . PHP_EOL;
                $level++;
                foreach ($item as $k => $v) {
                    $s .= $this->doRender($v, $k, $level);
                }
                $level--;
                $s .= $this->getChar('closeListChar', $item, $i, $level) . PHP_EOL;
            }
            else {
                $s .= str_replace('$value', $item, $this->getChar('getItem', $item, $i, $level));
            }
            $s .= $this->getChar('closeItemChar', $item, $i, $level) . PHP_EOL;
        }
        return $s;
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getChar($name, $value, $key, $level)
    {
        $m = $this->options[$name];
        $ret = '';
        if (is_string($m)) {
            $ret = $m;
        }
        else {
            $ret = call_user_func($m, $value, $key, $level);
        }
        return str_replace('$key', $key, $ret);
    }

    // it stays here so that we can copy paste it
    private function _itemRendererPrototype()
    {
        $levMax = 4;

        $itemRender = function ($item, $i, $level = 0) use (&$itemRender, $levMax) {
            $s = '';
            if ($level < $levMax) {
                $s .= '<li>' . PHP_EOL;
                $s .= $i . ': ' . PHP_EOL;
                if (is_array($item)) {
                    $s .= '<ul>' . PHP_EOL;
                    $level++;
                    foreach ($item as $k => $v) {
                        $s .= call_user_func($itemRender, $v, $k, $level);
                    }
                    $level--;
                    $s .= '</ul>' . PHP_EOL;
                }
                else {
                    $s .= $item;
                }
                $s .= '</li>' . PHP_EOL;
            }
            return $s;
        };
    }

}
