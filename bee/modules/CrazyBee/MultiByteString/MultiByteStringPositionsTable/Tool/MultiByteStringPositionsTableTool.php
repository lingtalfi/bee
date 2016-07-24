<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\MultiByteString\MultiByteStringPositionsTable\Tool;

use CrazyBee\MultiByteString\MultiByteStringPositionsTable\MultiByteStringPositionsTableUtil;
use CrazyBee\MultiByteString\MultiByteStringPositionsTable\Renderer\HtmlMultiByteStringPositionsTableRenderer;


/**
 * MultiByteStringPositionsTableTool
 * @author Lingtalfi
 * 2015-05-15
 *
 */
class MultiByteStringPositionsTableTool
{

    public static function displayHtmlPositionsTable($string, $padding = 2)
    {
        echo "
        <style>
            .mbstring_position_table,
            .mbstring_position_table tr,
            .mbstring_position_table td
            {
                border-collapse: collapse;
                border: 1px solid black;
            }
            
            .mbstring_position_table td{
                padding: {$padding}px;
            }
            
        </style>
        ";

        $o = new MultiByteStringPositionsTableUtil();
        $o->setRenderer(HtmlMultiByteStringPositionsTableRenderer::create()->setTableAttributes(['class' => 'mbstring_position_table']));
        echo $o->render($string);
    }
}
