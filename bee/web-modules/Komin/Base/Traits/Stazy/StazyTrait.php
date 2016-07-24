<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Traits\Stazy;

use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyTrait
 * @author Lingtalfi
 * 2015-01-30
 *
 *
 */
trait StazyTrait
{
    private static $inst;


    private function __construct()
    {

    }


    /**
     * You should only use a stazy service if you are sure that the plugin that contains the stazy service is activated.
     * That's why the method in comment is commented.
     */
    protected static function doGetInst($serviceId)
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService($serviceId);
        }
        return self::$inst;
    }


//    protected static function doGetInst($serviceId, $allowNull=false)
//    {
//        if (null === self::$inst) {
//            $mode = (false === $allowNull) ? 0 : 2;
//            self::$inst = StazyContainer::getInst()->getService($serviceId, false, $mode);
//        }
//        return self::$inst;
//    }
}
