<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\SessionToken\TokenManager\Stazy;

use WebModule\Komin\Base\Application\SessionToken\TokenManager\TokenManagerInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyTokenManager
 * @author Lingtalfi
 */
class StazyTokenManager
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return TokenManagerInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.application.tokenManager');
        }
        return self::$inst;
    }
}