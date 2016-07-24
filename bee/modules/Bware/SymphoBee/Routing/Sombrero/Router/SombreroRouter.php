<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Router;

use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Route\RouteInterface;
use Bware\SymphoBee\Routing\Router\RouterInterface;
use Bware\SymphoBee\Routing\Sombrero\Exception\SombreroException;


/**
 * SombreroRouter
 * @author Lingtalfi
 * 2015-06-02
 *
 */
abstract class SombreroRouter implements RouterInterface
{

    /**
     * Syntax errors are user errors that we can spot statically
     */
    protected function syntaxError($m)
    {
        $m = "Syntax Error: " . $m;
        throw new SombreroException($m);
    }

    /**
     * Syntax errors are user errors that we can only spot dynamically
     */
    protected function parseError($m)
    {
        $m = "Parse Error: " . $m;
        throw new SombreroException($m);
    }
    /**
     * Syntax errors are user errors that we can only spot dynamically
     */
    protected function problem($m)
    {
        $m = "Problem: " . $m;
        throw new SombreroException($m);
    }


}
