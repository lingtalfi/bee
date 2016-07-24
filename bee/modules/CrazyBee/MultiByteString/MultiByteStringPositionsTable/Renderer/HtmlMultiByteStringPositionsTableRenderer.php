<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\MultiByteString\MultiByteStringPositionsTable\Renderer;

use Bee\Bat\HtmlTool;


/**
 * HtmlMultiByteStringPositionsTableRenderer
 * @author Lingtalfi
 * 2015-05-15
 *
 */
class HtmlMultiByteStringPositionsTableRenderer implements MultiByteStringPositionsTableRendererInterface
{

    private $tableAttributes;

    public function __construct()
    {
        $this->tableAttributes = [];
    }


    public static function create()
    {
        return new static();
    }
    
    //------------------------------------------------------------------------------/
    // IMPLEMENTS MultiByteStringPositionsTableRendererInterface
    //------------------------------------------------------------------------------/
    public function render(array $chars)
    {
        /**
         * For now,
         * we will only render as an html table
         */

        $s = '';
        if ($chars) {
            $n = count($chars);
            $s .= '<table' . HtmlTool::toAttributesString($this->tableAttributes) . '>' . PHP_EOL;
            $s .= '<tr>' . PHP_EOL;
            for ($i = 0; $i < $n; $i++) {
                $s .= '<td>' . $i . '</td>' . PHP_EOL;
            }
            $s .= '</tr>' . PHP_EOL;


            $s .= '<tr>' . PHP_EOL;
            foreach ($chars as $char) {
                $s .= '<td>' . $char . '</td>' . PHP_EOL;
            }
            $s .= '</tr>' . PHP_EOL;
            $s .= '</table>' . PHP_EOL;
        }

        return $s;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setTableAttributes(array $tableAttributes)
    {
        $this->tableAttributes = $tableAttributes;
        return $this;
    }

}
