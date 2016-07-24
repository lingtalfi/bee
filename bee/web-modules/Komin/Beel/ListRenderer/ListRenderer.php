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


/**
 * ListRenderer
 * @author Lingtalfi
 * 2015-01-08
 *
 */
class ListRenderer implements ListRendererInterface
{

    /**
     * @param $itemRenderer callback with following signature
     *                                  string itemRenderer ( node, index )
     */
    protected $itemRenderer;

    function __construct($itemRenderer = null)
    {
        $this->itemRenderer = $itemRenderer;
    }


    public function render(array $items)
    {
        $s = '';
        foreach ($items as $i => $item) {
            $s .= call_user_func($this->itemRenderer, $item, $i);
        }
        return $s;
    }
}


