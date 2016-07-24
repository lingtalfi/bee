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

/**
 * RequestEvent
 * @author Lingtalfi
 *
 *
 */
class RequestEvent extends WithResponseEvent
{


    protected $controllerCallback;
    protected $controllerCallbackArgs;

    public function __construct(HttpRequestInterface $request)
    {
        parent::__construct($request);
        $this->controllerCallbackArgs = [];
    }


    public function setControllerCallbackAndArgs($controllerCallback, array $args)
    {
        $this->controllerCallback = $controllerCallback;
        $this->controllerCallbackArgs = $args;
    }


    public function getControllerCallbackAndArgs()
    {
        return array($this->controllerCallback, $this->controllerCallbackArgs);
    }

    public function hasControllerCallbackAndArgs()
    {
        return (null !== $this->controllerCallback);
    }
}
