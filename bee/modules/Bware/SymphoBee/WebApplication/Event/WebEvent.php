<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\WebApplication\Event;


use Bee\Abstractive\EventDispatcher\Event;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;


/**
 * Event
 * @author Lingtalfi
 */
class WebEvent extends Event
{

    /**
     * @var HttpRequestInterface
     */
    protected $request;


    public function __construct(HttpRequestInterface $request)
    {
        $this->request = $request;
    }


    public function setHttpRequest(HttpRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return HttpRequestInterface
     */
    public function getHttpRequest()
    {
        return $this->request;
    }


}
