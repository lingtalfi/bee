<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Route;

use Bee\Component\Bag\BagInterface;
use Bee\Component\Bag\BdotBag;
use Bware\SymphoBee\Routing\Controller\ControllerInterface;
use Bware\SymphoBee\Routing\Exception\RoutingException;


/**
 * Route
 * @author Lingtalfi
 * 2015-06-01
 *
 */
class Route implements RouteInterface
{

    private $name;
    private $controller;
    private $args;
    private $context;
    private $matchTest;

    public function __construct()
    {
        $this->args = [];
        $this->context = new BdotBag();

    }


    public static function create()
    {
        return new static();
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS RouteInterface
    //------------------------------------------------------------------------------/
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return RouteInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return callable|ControllerInterface|null
     */
    public function getController()
    {
        return $this->controller;
    }

    public function setController(callable $controller)
    {
        if (
            is_callable($controller) ||
            $controller instanceof ControllerInterface
        ) {
            $this->controller = $controller;
        }
        else {
            throw new RoutingException("Controller argument must be a callable or a ControllerInterface");
        }
        return $this;
    }

    /**
     * @return []
     */
    public function getControllerArgs()
    {
        return $this->args;
    }

    public function setControllerArgs(array $args)
    {
        $this->args = $args;
        return $this;
    }

    /**
     * @return RouteInterface
     */
    public function setControllerArg($name, $value)
    {
        $this->args[$name] = $value;
        return $this;
    }


    /**
     * @return BagInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setContext(BagInterface $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return
     *           bool   callable ( HttpRequest )
     */
    public function getMatchTest()
    {
        return $this->matchTest;
    }

    public function setMatchTest(callable $matchTest)
    {
        $this->matchTest = $matchTest;
        return $this;
    }

}
