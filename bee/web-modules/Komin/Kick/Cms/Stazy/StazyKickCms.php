<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Kick\Cms\Stazy;


use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Kick\Cms\KickCmsInterface;


/**
 * StazyKickCms
 * @author Lingtalfi
 * 2015-01-13
 *
 */
class StazyKickCms
{


    private static $inst;


    private function __construct()
    {
    }


    /**
     * @return KickCmsInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.kick.cms');
        }
        return self::$inst;
    }

}
