<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base;

use Bee\Application\Kernel\Kcp\KcpKernelInterface;
use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use WebModule\Komin\Base\Core\Kernel\KernelOptionsGetter;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Log\SuperLogger\SuperLoggerStarterInterface;


/**
 * Initializer
 * @author Lingtalfi
 * 2014-10-12
 *
 */
class Initializer
{


    public function init(ServiceContainerInterface $container)
    {
        // initializing container for all stazy services
        StazyContainer::setInst($container);
        // initializing superLogger from a service
        if (false !== $starter = $container->getService('komin.base.log.superLoggerStarter', false)) {
            /**
             * @var SuperLoggerStarterInterface $starter
             */
            $starter->start();
        }

    }

}
