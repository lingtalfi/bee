<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Tool;

use Bware\SymphoBee\HttpResponse\HttpResponseInterface;
use Bware\SymphoBee\Routing\Exception\RoutingException;
use Bware\SymphoBee\Routing\Route\RouteInterface;


/**
 * RoutingTool
 * @author Lingtalfi
 * 2015-06-02
 *
 */
class RoutingTool
{


    /**
     * @param RouteInterface $r
     * @return HttpResponseInterface
     */
    public static function executeControllerByRoute(RouteInterface $r)
    {
        $c = $r->getController();
        if (is_callable($c)) {
            $r = call_user_func_array($c, $r->getControllerArgs());
            if ($r instanceof HttpResponseInterface) {
                return $r;
            }
            else {
                throw new RoutingException("The Controller given by the route must return an HttpResponseInterface");
            }
        }
        else {
            throw new RoutingException("This route hasn't define a Controller yet");
        }
    }
}
