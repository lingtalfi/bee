<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Route;

use Bware\SymphoBee\Routing\Exception\RoutingException;
use Bware\SymphoBee\Routing\Route\RouteInterface;
use Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * SombreroRouteInterface
 * @author Lingtalfi
 * 2015-06-02
 *
 */
interface SombreroRouteInterface extends RouteInterface
{


    public function getUriVars();

    public function hasUriVar($name);

    /**
     * @throws RoutingException
     */
    public function getUriVar($name);

    /**
     * @return SombreroRouteInterface
     */
    public function setUriVars(array $vars);

    /**
     * @return SombreroRouteInterface
     */
    public function setUriMatcher(UriMatcherInterface $m);

    /**
     * @return UriMatcherInterface
     */
    public function getUriMatcher();
}
