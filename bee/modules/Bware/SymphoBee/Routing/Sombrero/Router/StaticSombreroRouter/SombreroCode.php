<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter;


/**
 * SombreroCode
 * @author Lingtalfi
 * 2015-06-02
 *
 */
class SombreroCode
{

    private $fragments;

    public function __construct()
    {
        $this->fragments = [];
    }

    public static function create()
    {
        return new static();
    }

    public function add($string)
    {
        $this->fragments[] = $string;
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function __toString()
    {
        return implode(PHP_EOL, $this->fragments);
    }


}
