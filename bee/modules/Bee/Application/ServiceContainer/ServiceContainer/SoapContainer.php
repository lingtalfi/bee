<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Application\ServiceContainer\ServiceContainer;

use Bee\Application\ServiceContainer\Exception\ServiceContainerException;


/**
 * SoapContainer
 * @author Lingtalfi
 * 2015-05-29
 *
 */
class SoapContainer
{

    private static $inst;

    public static function setContainer(ServiceContainerInterface $container)
    {
        if (null === self::$inst) {
            self::$inst = $container;
        }
        else {
            throw new ServiceContainerException("setContainer method has already been called");
        }
    }

    /**
     * @return ServiceContainerInterface
     */
    public static function getContainer()
    {
        return self::$inst;
    }
}
