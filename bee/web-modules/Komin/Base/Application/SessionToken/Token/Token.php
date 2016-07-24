<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\SessionToken\Token;

use Bee\Component\Http\Session\SessionInterface;



/**
 * Token
 * @author Lingtalfi
 *
 *
 */
class Token implements TokenInterface
{

    private $key;

    public function __construct(SessionInterface $session, $key)
    {
        $this->session = $session;
        $this->key = $key;
    }

    public function set($key, $value)
    {
        $this->session->set($this->getRealKey($key), $value);
    }

    public function get($key, $default = null)
    {
        return $this->session->get($this->getRealKey($key), $default);
    }

    public function has($key)
    {
        return $this->session->has($this->getRealKey($key));
    }
    
    public function all()
    {
        return $this->session->get($this->key);
    }

    public function unsetValue($key)
    {
        $this->session->unsetValue($this->getRealKey($key));
    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function getRealKey($key)
    {
        return $this->key . '.' . $key;
    }
}
