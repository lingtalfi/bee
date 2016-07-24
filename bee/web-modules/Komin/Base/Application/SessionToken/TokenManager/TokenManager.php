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


use WebModule\Komin\Base\Application\SessionToken\Token\Token;
use WebModule\Komin\Base\Application\SessionToken\Token\TokenInterface;
use WebModule\Komin\Base\Http\Session\Stazy\StazySession;


/**
 * TokenManager
 * @author Lingtalfi
 *
 *
 */
class TokenManager implements TokenManagerInterface
{

    protected $options;
    protected $key;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            /**
             * If not false,
             * each call to the getToken method sets the new expiration
             * time to:
             *      now + <renewValue>  (timestamp in seconds)
             */
            'renew' => 3600,
            'duration' => 3600,
        ], $options);
        $this->key = '_kominToken';
    }


    /**
     * @return TokenInterface
     */
    public function getFreshToken()
    {
        if (false !== $t = $this->getToken()) {
            return $t;
        }
        return $this->getNewToken();
    }

    /**
     * @return TokenInterface|false, false is the token has expired
     */
    public function getToken()
    {
        $session = StazySession::getInst();
        $ret = false;
        $t = time();
        if (false !== $data = $session->get($this->key, false)) {
            if ($data['_expiresAt'] >= $t) {
                $ret = new Token($session, $this->key);
                if (false !== $d = $this->options['renew']) {
                    $newX = $t + (int)$d;
                    $ret->set('_expiresAt', $newX);
                }
            }
        } else {
            // creating new token
            $ret = $this->getNewToken();
        }
        return $ret;
    }

    public function destroyToken()
    {
        StazySession::getInst()->unsetValue($this->key);
        return $this;
    }

    public function tokenHasExpired()
    {
        if (false !== $token = $this->getToken()) {
            return (time() >= $token->get('_expiresAt'));
        }
        return true;
    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function getNewToken()
    {
        $ret = new Token(StazySession::getInst(), $this->key);
        $ret->set('_expiresAt', time() + (int)$this->options['duration']);
        return $ret;
    }
}
