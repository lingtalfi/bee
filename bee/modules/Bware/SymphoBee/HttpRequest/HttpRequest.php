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
use Bee\Component\Bag\ReadOnlyBagInterface;
use Bee\Component\Bag\ReadOnlyBdotBag;
use Bware\SymphoBee\HttpRequest\Tool\PhpFilesAdaptorTool;


/**
 * HttpRequest
 * @author Lingtalfi
 * 2015-06-01
 *
 * This implementation was done using Apache,
 * but it might also work with other webServers (hence I did not change the name for ApacheHttpRequest yet).
 *
 *
 */
class HttpRequest implements HttpRequestInterface
{

    /**
     * @var ReadOnlyBagInterface
     */
    protected $header;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $server;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $cookie;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $session;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $file;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $get;
    /**
     * @var ReadOnlyBagInterface
     */
    protected $post;


    public function __construct()
    {

    }

    public static function create()
    {
        return new static();
    }
    //------------------------------------------------------------------------------/
    // BASIC REQUEST INFO
    //------------------------------------------------------------------------------/
    public function scheme()
    {
        $ret = 'http';
        $https = $this->server()->get('HTTPS', '');
        if (
            '' !== $https &&
            'off' !== $https  // ISAPI with IIS
        ) {
            $ret = 'https';
        }
        return $ret;
    }

    public function host()
    {
        return $this->server()->get('HTTP_HOST');
    }

    public function uri()
    {
        return $this->server()->get('REQUEST_URI');
    }

    public function queryString()
    {
        return $this->server()->get('QUERY_STRING');
    }

    //------------------------------------------------------------------------------/
    // MISCELLANEOUS REQUEST INFO
    //------------------------------------------------------------------------------/
    public function ip()
    {
        return $this->server()->get('REMOTE_ADDR');
    }

    public function port()
    {
        return (int)$this->server()->get('SERVER_PORT');
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/


    /**
     * @return ReadOnlyBagInterface
     */
    public function header()
    {
        if (null === $this->header) {
            $this->header = new ReadOnlyBag(apache_request_headers());
        }
        return $this->header;
    }

    /**
     * @return ReadOnlyBagInterface
     */
    public function server()
    {
        if (null === $this->server) {
            $this->server = new ReadOnlyBag($_SERVER);
        }
        return $this->server;
    }

    /**
     * @return ReadOnlyBagInterface
     */
    public function cookie()
    {
        if (null === $this->cookie) {
            $this->cookie = new ReadOnlyBag($_COOKIE);
        }
        return $this->cookie;
    }


    /**
     * @return ReadOnlyBagInterface
     */
    public function session()
    {
        if (null === $this->session) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $this->session = new ReadOnlyBdotBag($_SESSION);
        }
        return $this->session;
    }

    /**
     * @return ReadOnlyBagInterface
     */
    public function file()
    {
        if (null === $this->file) {
            $files = [];
            if ($_FILES) {
                $files = PhpFilesAdaptorTool::getFormattedFilesArray($_FILES);
            }
            $this->file = new ReadOnlyBdotBag($files);
        }
        return $this->file;
    }

    /**
     * @return ReadOnlyBagInterface
     */
    public function get()
    {
        if (null === $this->get) {
            $this->get = new ReadOnlyBdotBag($_GET);
        }
        return $this->get;
    }

    /**
     * @return ReadOnlyBagInterface
     */
    public function post()
    {
        if (null === $this->post) {
            $this->post = new ReadOnlyBdotBag($_POST);
        }
        return $this->post;
    }


}
