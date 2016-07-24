<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Installer\Installer;

use WebModule\Komin\Installer\TaskLogger\TaskLoggerInterface;


/**
 * InstallerInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 */
interface InstallerInterface
{

    /**
     * @param array $taskDescription , defined in the target factory
     * @return TaskLoggerInterface
     */
    public function install(array $taskDescription);

    /**
     * @param array $taskDescription , defined in the target factory
     * @return TaskLoggerInterface
     */
    public function uninstall(array $taskDescription);
}
