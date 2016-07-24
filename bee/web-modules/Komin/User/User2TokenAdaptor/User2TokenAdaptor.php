<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\User2TokenAdaptor;


/**
 * User2TokenAdaptor
 * @author Lingtalfi
 *
 *
 */
class User2TokenAdaptor implements User2TokenAdaptorInterface
{

    protected $userDataKeys;
    protected $badgesKey;

    public function __construct(array $userDataKeys = [], $badgesKey)
    {
        $this->userDataKeys = $userDataKeys;
        $this->badgesKey = $badgesKey;
    }


    /**
     * @return array:
     *      - array     userData
     *      - array     badges
     */
    public function getUserData(array $user)
    {
        $ret = [
            'userData' => [],
            'badges' => [],
        ];
        foreach ($user as $k => $v) {
            if (in_array($k, $this->userDataKeys, true)) {
                $ret['userData'][$k] = $v;
            } elseif ($this->badgesKey === $k) {
                $ret['badges'] = $v;
            }
        }
        return $ret;
    }
}
