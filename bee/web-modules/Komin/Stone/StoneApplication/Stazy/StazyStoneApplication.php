<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Stone\StoneApplication\Stazy;


use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Stone\StoneApplication\StoneApplicationInterface;


/**
 * StazyStoneApplication
 * @author Lingtalfi
 */
class StazyStoneApplication
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return StoneApplicationInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.stone.application');
        }
        return self::$inst;
    }
}
