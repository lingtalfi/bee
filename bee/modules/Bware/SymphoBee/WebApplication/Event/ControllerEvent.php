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
use WebModule\Bware\SymphoBee\Controller\ControllerInterface;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;


/**
 * ControllerEvent
 * @author Lingtalfi
 *
 *
 */
class ControllerEvent extends WebEvent
{


    /**
     * @var ControllerInterface
     */
    protected $controller;

    public function __construct(HttpRequestInterface $request, ControllerInterface $controller)
    {
        parent::__construct($request);
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }



}
