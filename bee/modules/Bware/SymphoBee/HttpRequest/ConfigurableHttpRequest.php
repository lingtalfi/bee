<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\HttpRequest;

use Bee\Component\Bag\ReadOnlyBag;
use Bee\Component\Bag\ReadOnlyBdotBag;


/**
 * ConfigurableHttpRequest
 * @author Lingtalfi
 * 2015-06-03
 *
 *
 *
 */
class ConfigurableHttpRequest extends HttpRequest
{
    private $scheme;
    private $host;
    private $uri;
    private $queryString;
    private $ip;
    private $port;

    //------------------------------------------------------------------------------/
    // BASIC REQUEST INFO
    //------------------------------------------------------------------------------/
    public function scheme()
    {
        if (null === $this->scheme) {
            return parent::scheme();
        }
        return $this->scheme;
    }

    public function host()
    {
        if (null === $this->host) {
            return parent::host();
        }
        return $this->host;
    }

    public function uri()
    {
        if (null === $this->uri) {
            return parent::uri();
        }
        return $this->uri;
    }

    public function queryString()
    {
        if (null === $this->queryString) {
            return parent::queryString();
        }
        return $this->queryString;
    }

    //------------------------------------------------------------------------------/
    // MISCELLANEOUS REQUEST INFO
    //------------------------------------------------------------------------------/
    public function ip()
    {
        if (null === $this->ip) {
            return parent::ip();
        }
        return $this->ip;
    }

    public function port()
    {
        if (null === $this->port) {
            return parent::port();
        }
        return $this->port;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
        return $this;
    }

    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setHeader(array $header)
    {
        $this->header = new ReadOnlyBag($header);
        return $this;
    }
    public function setServer(array $server)
    {
        $this->server = new ReadOnlyBag($server);
        return $this;
    }
    public function setCookie(array $cookie)
    {
        $this->cookie = new ReadOnlyBag($cookie);
        return $this;
    }
    public function setSession(array $session)
    {
        $this->session = new ReadOnlyBdotBag($session);
        return $this;
    }
    public function setFile(array $file)
    {
        $this->file = new ReadOnlyBdotBag($file);
        return $this;
    }
    public function setGet(array $get)
    {
        $this->get = new ReadOnlyBdotBag($get);
        return $this;
    }
    public function setPost(array $post)
    {
        $this->post = new ReadOnlyBdotBag($post);
        return $this;
    }

}
