<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Application\Kernel\Kcp;

use Bee\Application\Kernel\KernelInterface;
use Bee\Application\ServiceContainer\ServiceContainer\ContainerInterface;


/**
 * KcpKernelInterface
 * @author Lingtalfi
 * 2014-08-21
 *
 */
interface KcpKernelInterface extends KernelInterface
{


    /**
     * @return ContainerInterface
     */
    public function getContainer();

    public function setContainer(ContainerInterface $container);
}
