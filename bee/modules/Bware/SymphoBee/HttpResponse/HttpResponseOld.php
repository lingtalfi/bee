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
class HttpResponseOld implements HttpResponseInterface
{

    protected $httpVersion = 'HTTP/1.1';
    protected $statusCode;
    protected $reasonPhrase;
    protected $headers;
    private $body;


    public function __construct($body = null, $statusCode = 200, $reasonPhrase = null, array $headers = [], $httpVersion = 'HTTP/1.1')
    {
        if (null === $reasonPhrase && array_key_exists($statusCode, self::$reasonPhrases)) {
            $reasonPhrase = self::$reasonPhrases[$statusCode];
        }
        $this->body = (string)$body;
        $this->httpVersion = (string)$httpVersion;
        $this->statusCode = (int)$statusCode;
        $this->reasonPhrase = (string)$reasonPhrase;
        $this->headers = $headers;
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS HttpResponseInterface
    //------------------------------------------------------------------------------/
    /**
     * @see ResponseInterface
     * @inheritDoc
     */
    public function send()
    {
        // status Line
        header($this->httpVersion . ' ' . $this->statusCode . ' ' . $this->reasonPhrase);

        // headers
        foreach ($this->headers as $name => $value) {
            if (!is_array($value)) {
                header($name . ': ' . $value, false);
            }
            else {
                foreach ($value as $v) {
                    header($name . ': ' . $v, false);
                }
            }
        }
        echo $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }




}
