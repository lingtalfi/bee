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
 * PhpCode
 * @author Lingtalfi
 * 2015-06-08
 *
 */
class PhpCode
{

    private $code;

    public function __construct()
    {
        $this->code = '';
    }


    public static function create()
    {
        return new static();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }


}
