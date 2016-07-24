<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\UserToken\Stazy;

use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\User\UserToken\UserTokenInterface;


/**
 * StazyUserToken
 * @author Lingtalfi
 */
class StazyUserToken
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return UserTokenInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.user.userToken');
        }
        return self::$inst;
    }
}