<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\MultiByteString\MultiByteStringPositionsTable;


use Bee\Bat\StringTool;
use CrazyBee\MultiByteString\MultiByteStringPositionsTable\Renderer\MultiByteStringPositionsTableRendererInterface;



/**
 * MultiByteStringPositionsTableUtil
 * @author Lingtalfi
 * 2015-05-15
 *
 */
class MultiByteStringPositionsTableUtil
{

    /**
     * @var MultiByteStringPositionsTableRendererInterface
     */
    private $renderer;

    public function render($string)
    {
        // asume working with utf8, 
        // use CrazyBee/MultiByteString/Tool/MultiByteStringTool.getByteSafeChars otherwise
        $chars = StringTool::getChars($string);
        return $this->renderChars($chars);
    }


    public function setRenderer(MultiByteStringPositionsTableRendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function renderChars(array $chars)
    {
        if (null === $this->renderer) {
            throw new \LogicException("please set the renderer BEFORE you use the renderChars method");
        }
        return $this->renderer->render($chars);
    }
}
