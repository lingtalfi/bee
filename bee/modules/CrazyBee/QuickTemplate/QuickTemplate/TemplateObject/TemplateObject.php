<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\QuickTemplate\QuickTemplate\TemplateObject;


/**
 * TemplateObject
 * @author Lingtalfi
 * 2015-05-28
 *
 */
class TemplateObject implements TemplateObjectInterface
{

    private $content;

    public function __construct()
    {
        $this->content = '';
    }

    public static function create()
    {
        return new static();
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS TemplateObjectInterface
    //------------------------------------------------------------------------------/
    public function getContent()
    {
        return $this->content;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/    
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }


}
