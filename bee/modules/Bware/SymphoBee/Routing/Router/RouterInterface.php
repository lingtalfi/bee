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
 * RouterInterface
 * @author Lingtalfi
 * 2015-06-01
 *
 */
interface RouterInterface
{


    /**
     * Returns a matching route, or false
     * 
     * @param HttpRequestInterface $r
     * @return RouteInterface|false
     */
    public function match(HttpRequestInterface $r);

    
}
