<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Soap\Bware\SymphoBee\Routing\Sombrero\DynamicRouter;
use Bee\Application\ServiceContainer\ServiceContainer\SoapContainer;
use Bware\SymphoBee\Routing\Sombrero\Router\DynamicSombreroRouter;


/**
 * SoapDynamicRouter
 * @author Lingtalfi
 * 2015-06-09
 * 
 */
class SoapDynamicRouter {

    /**
     * @return DynamicSombreroRouter
     */
    public static function get(){
        return SoapContainer::getContainer()->getService('bware.symphoBee.routing.sombrero.dynamicRouter');
    }
}
