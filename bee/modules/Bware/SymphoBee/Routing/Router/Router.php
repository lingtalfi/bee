<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Router;

use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Route\RouteInterface;


/**
 * Router
 * @author Lingtalfi
 * 2015-06-01
 *
 */
class Router implements RouterInterface
{

    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public static function create()
    {
        return new static();
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS RouterInterface
    //------------------------------------------------------------------------------/
    /**
     * @param HttpRequestInterface $r
     * @return RouteInterface|false
     */
    public function match(HttpRequestInterface $r)
    {
        foreach ($this->routes as $name => $route) {
            /**
             * @var RouteInterface $route
             */
            $f = $route->getMatchTest();
            if (true === call_user_func($f, $r, $route)) {
                $this->onRouteMatch($route, $name);
                return $route;
            }
        }
        return false;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setRoute(RouteInterface $r, $index = null)
    {
        if (null === $index) {
            $this->routes[] = $r;
        }
        else {
            $this->routes[$index] = $r;
        }
        return $this;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function onRouteMatch(RouteInterface $r, $name)
    {
        $r->setName($name);
    }

}
