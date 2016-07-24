<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\Granter;

use WebModule\Komin\User\UserToken\UserTokenTool;
use Komin\User\Granter\GranterInterface;


/**
 * Granter
 * @author Lingtalfi
 * 2015-02-01
 *
 */
class Granter implements GranterInterface
{
    /**
     * @param $badges , string|array
     * @return bool, true if the user is granted to perform the action
     */
    public function isGranted($badges)
    {
        return UserTokenTool::hasBadges($badges);
    }


}
