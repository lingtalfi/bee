<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\HttpResponse;

use Bware\SymphoBee\HttpResponse\Tool\HttpResponseTool;


/**
 * HttpResponse
 * @author Lingtalfi
 * 2015-06-01
 *
 * In this implementation, the first header is handled differently than the others, because it is a mandatory one.
 * The first header looks like this:
 *      HTTP/1.1 200 OK
 *
 *
 */
class HttpResponse implements HttpResponseInterface
{

    private $headers;
    private $body;

    private $httpVersion;
    private $statusCode;
    private $reasonPhrase;

    public function __construct()
    {
        $this->body = '';
        $this->headers = [];
        $this->httpVersion = 'HTTP/1.1';
        $this->statusCode = 200;
        $this->reasonPhrase = 'OK';
    }

    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS HttpResponseInterface
    //------------------------------------------------------------------------------/
    public function send()
    {

        // status Line
        header($this->httpVersion . ' ' . $this->statusCode . ' ' . $this->reasonPhrase);

        // headers
        foreach ($this->headers as $info) {
            list($string, $replace) = $info;
            header($string, $replace);
        }
        echo $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return HttpResponseInterface
     */
    public function setHeader($string, $replace = true)
    {
        $this->headers[] = [$string, $replace];
        return $this;
    }

    /**
     * @return array of
     *                  0: string header
     *                  1: bool replace
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/   
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;
        return $this;
    }

    public function setReasonPhrase($reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
        return $this;
    }

    public function setStatusCode($statusCode, $changePhrase = true)
    {
        $this->statusCode = $statusCode;
        if (true === $changePhrase) {
            if (array_key_exists($statusCode, HttpResponseTool::$reasonPhrases)) {
                $this->setReasonPhrase(HttpResponseTool::$reasonPhrases[$statusCode]);
            }
        }
        return $this;
    }


}
