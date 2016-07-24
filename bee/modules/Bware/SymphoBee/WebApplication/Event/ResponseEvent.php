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

use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use WebModule\Bware\SymphoBee\HttpResponse\HttpResponseInterface;


/**
 * ResponseEvent
 * @author Lingtalfi
 *
 *
 */
class ResponseEvent extends WithResponseEvent
{

    public function __construct(HttpRequestInterface $request, HttpResponseInterface $response)
    {
        $this->setResponse($response);
        parent::__construct($request);
    }
}
