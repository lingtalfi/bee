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
 * ArrayUserList
 * @author Lingtalfi
 * 2014-12-24
 *
 */
class ArrayUserList implements UserListInterface
{

    protected $users;
    protected $options;


    public function __construct(array $users = [], array $options = [])
    {
        $this->options = array_replace([
            'loginKey' => 'login',
            'passKey' => 'pass',
        ], $options);
        $this->users = $users;
    }

    /**
     * @return array|false
     */
    public function getUser(array $credentials)
    {
        $login = $credentials['login'];
        $pass = $credentials['pass'];
        foreach ($this->users as $user) {
            if (
                $login === $user[$this->options['loginKey']] &&
                $pass === $user[$this->options['passKey']]
            ) {
                return $user;
            }
        }
        return false;
    }
}
