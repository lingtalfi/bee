<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\UserList;


/**
 * UserListInterface
 * @author Lingtalfi
 * 2014-12-24
 *
 */
interface UserListInterface
{

    /**
     * @return array|false
     */
    public function getUser(array $credentials);
}
