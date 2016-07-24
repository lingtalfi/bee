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
use Bware\SymphoBee\Routing\Controller\ControllerInterface;


/**
 * RouteInterface
 * @author Lingtalfi
 * 2015-06-01
 *
 */
interface RouteInterface
{


    public function getName();

    /**
     * @return RouteInterface
     */
    public function setName($name);

    /**
     * @return callable|ControllerInterface|null
     */
    public function getController();

    /**
     * @param $controller
     * @return RouteInterface
     */
    public function setController(callable $controller);

    /**
     * @return []
     */
    public function getControllerArgs();

    /**
     * @param array $args
     * @return RouteInterface
     */
    public function setControllerArgs(array $args);

    /**
     * @return RouteInterface
     */
    public function setControllerArg($name, $value);

    /**
     * @return BagInterface
     */
    public function getContext();

    /**
     * @param BagInterface $context
     * @return RouteInterface
     */
    public function setContext(BagInterface $context);

    public function getMatchTest();

    /**
     * @param callable $matchTest
     *                  bool    callable ( HttpRequest, ThisRouteInterface )
     * @return RouteInterface
     */
    public function setMatchTest(callable $matchTest);

}
