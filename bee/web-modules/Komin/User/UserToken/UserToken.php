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
use WebModule\Komin\Base\Application\SessionToken\TokenManager\Stazy\StazyTokenManager;
use WebModule\Komin\User\BadgeList\BadgeListInterface;
use WebModule\Komin\User\User2TokenAdaptor\User2TokenAdaptorInterface;
use WebModule\Komin\User\UserList\UserListInterface;


/**
 * UserToken
 * @author Lingtalfi
 *
 *
 */
class UserToken implements UserTokenInterface
{

    /**
     * @var UserListInterface
     */
    protected $userList;
    protected $user2TokenAdaptor;
    /**
     * @var BadgeListInterface
     */
    protected $badgesList;

    public function __construct(
        UserListInterface $userList,
        User2TokenAdaptorInterface $user2Token,
        BadgeListInterface $badgeList
    )
    {
        $this->userList = $userList;
        $this->user2TokenAdaptor = $user2Token;
        $this->badgesList = $badgeList;
    }


    /**
     * @return TokenInterface|false
     */
    public function connect(array $credentials)
    {
        if (false !== $user = $this->userList->getUser($credentials)) {
            $userData = $this->user2TokenAdaptor->getUserData($user);
            $token = StazyTokenManager::getInst()->getFreshToken();
            $token->set('userData', $userData['userData']);
            $token->set('badges', $this->badgesList->getBadges($userData['badges']));
            return $token;
        }
        return false;
    }

    public function disconnect()
    {
        StazyTokenManager::getInst()->destroyToken();
    }

    public function hasBadges($badges)
    {
        if (empty($badges)) {
            return true;
        }
        
        if (false !== $token = StazyTokenManager::getInst()->getToken()) {
            $myBadges = $token->get('badges', []);
            if (!is_array($badges)) {
                $badges = [$badges];
            }
           
            foreach ($badges as $badge) {
                if (in_array($badge, $myBadges, true)) {
                    return true;
                }
            }
        }
        return false;
    }
}
