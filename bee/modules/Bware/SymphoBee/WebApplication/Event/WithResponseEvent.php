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

use WebModule\Bware\SymphoBee\HttpResponse\HttpResponseInterface;

/**
 * WithResponseEvent
 * @author Lingtalfi
 *
 *
 */
class WithResponseEvent extends WebEvent
{

    /**
     * @var HttpResponseInterface
     */
    protected $response;

    public function setResponse(HttpResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return HttpResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function hasResponse()
    {
        return (null !== $this->response);
    }


}
