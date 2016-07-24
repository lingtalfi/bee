<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\WebApplication\Dispatching\Listener;

use WebModule\Bware\SymphoBee\Routing\Router\ColombusRouter;
use WebModule\Bware\SymphoBee\WebApplication\Event\RequestEvent;


/**
 * ColombusRouterListener
 * @author Lingtalfi
 * 2015-03-10
 *
 * This object's goal is to inject the Controller responsible for handling
 * the incoming HttpRequest into the event.
 *
 *
 */
class ColombusRouterListener
{


    /**
     * @var string $defaultController , controller string (<className> <::> <method>)
     */
    protected $defaultController;
    protected $defaultControllerConstructorArgs;

    /**
     * @var ColombusRouter
     */
    protected $router;

    public function __construct(ColombusRouter $router, array $options = [])
    {
        $this->router = $router;
        $options = array_replace([
            'defaultController' => null,
            'defaultControllerConstructorArgs' => [],
        ], $options);
        $this->defaultController = $options['defaultController'];
        $this->defaultControllerConstructorArgs = $options['defaultControllerConstructorArgs'];
    }


    public function listen(RequestEvent $event)
    {
        $controllerString = $this->defaultController;
        $cArgs = $this->defaultControllerConstructorArgs;
        $vars = [];

        if (false !== $routeNode = $this->router->match($event->getHttpRequest())) {
            /**
             * Route matching ?
             * Let's instantiate and store the controller
             */
            if (array_key_exists('controller', $routeNode)) {
                $controllerString = $routeNode['controller'];
                $cArgs = (array_key_exists('controllerConstructorArgs', $routeNode)) ? $routeNode['controllerConstructorArgs'] : [];
                $vars = (array_key_exists('vars', $routeNode)) ? $routeNode['vars'] : [];
            }
            else {
                throw new \LogicException("routeNode.controller not found");
            }
        }

        if (null !== $controllerString) {
            $p = explode('::', $controllerString);
            if (2 === count($p)) {
                $class = new \ReflectionClass($p[0]);
                $instance = $class->newInstanceArgs($cArgs);
                $callback = [$instance, $p[1]];
                /**
                 * Now we need to find out the controller's method arguments
                 */
                $args = $this->getControllerArgs($p[0], $p[1], $vars);
                $event->setControllerCallbackAndArgs($callback, $args);
            }
            else {
                throw new \InvalidArgumentException(sprintf("routeNodeController must contain two components separated with the :: symbol, %s given", $controllerString));
            }
        }

    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getControllerArgs($controllerName, $method, array $vars)
    {
        $ret = [];
        $o = new \ReflectionMethod($controllerName, $method);
        $params = $o->getParameters();
        foreach ($params as $p) {
            $name = $p->getName();

            if (false === $p->isOptional()) {
                // mandatory argument must be found in vars
                if (array_key_exists($name, $vars)) {
                    $ret[] = $vars[$name];
                }
                else {
                    throw new \RuntimeException(sprintf("Cannot find the value for param %s of controller %s with the given route vars", $name, $controllerName));
                }
            }
            else {
                // optional arguments, if not found in vars, are given their default value
                if (array_key_exists($name, $vars)) {
                    $ret[] = $vars[$name];
                }
                else {
                    if (true === $p->isDefaultValueAvailable()) {
                        $ret[] = $p->getDefaultValue();
                    }
                    else {
                        throw new \RuntimeException(sprintf("Cannot find a default value for param %s of controller %s", $name, $controllerName));
                    }
                }
            }
        }
        return $ret;
    }


}
