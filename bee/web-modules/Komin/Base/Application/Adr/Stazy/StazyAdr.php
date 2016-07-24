<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\Adr\Stazy;

use WebModule\Komin\Base\Application\Adr\AdrInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyAdr
 * @author Lingtalfi
 * 2014-10-21
 *
 */
class StazyAdr
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return AdrInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.application.adr');
        }
        return self::$inst;
    }
}
