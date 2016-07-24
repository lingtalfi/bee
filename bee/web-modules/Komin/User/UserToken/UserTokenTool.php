<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\UserToken;

use WebModule\Komin\User\UserToken\Stazy\StazyUserToken;


/**
 * UserTokenTool
 * @author Lingtalfi
 *
 *
 */
class UserTokenTool
{

    public static function hasBadges($badges)
    {
        return StazyUserToken::getInst()->hasBadges($badges);
    }

    public static function connect(array $credentials)
    {
        return StazyUserToken::getInst()->connect($credentials);
    }

    public static function disconnect()
    {
        StazyUserToken::getInst()->disconnect();
    }
}
