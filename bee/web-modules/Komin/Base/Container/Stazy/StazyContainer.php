<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Container\Stazy;
use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;


/**
 * StazyContainer
 * @author Lingtalfi
 * 2014-10-12
 *
 */
class StazyContainer
{

    private static $container;

    public static function setInst(ServiceContainerInterface $container)
    {
        self::$container = $container;
    }


    /**
     * @return ServiceContainerInterface
     */
    public static function getInst()
    {
        return self::$container;
    }

}
