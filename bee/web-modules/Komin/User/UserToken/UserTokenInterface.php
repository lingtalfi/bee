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
use WebModule\Komin\Base\Application\SessionToken\Token\TokenInterface;


/**
 * UserTokenInterface
 * @author Lingtalfi
 *
 *
 */
interface UserTokenInterface
{

    /**
     * @return TokenInterface|false
     */
    public function connect(array $credentials);

    public function disconnect();

    public function hasBadges($badges);
}
