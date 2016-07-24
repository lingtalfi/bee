<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\SessionToken\TokenManager;

use WebModule\Komin\Base\Application\SessionToken\Token\TokenInterface;


/**
 * TokenManagerInterface
 * @author Lingtalfi
 *
 *
 */
interface TokenManagerInterface
{


    /**
     * @return TokenInterface|false, false is the token has expired
     */
    public function getToken();

    /**
     * @return TokenInterface, tries to getToken, and if false, creates a new one
     */
    public function getFreshToken();

    public function tokenHasExpired();

    /**
     * @return TokenManagerInterface
     */
    public function destroyToken();
}
