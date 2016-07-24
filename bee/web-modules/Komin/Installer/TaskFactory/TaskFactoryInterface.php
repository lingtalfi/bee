<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Installer\TaskFactory;

use WebModule\Komin\Installer\Task\TaskInterface;


/**
 * TaskFactoryInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 */
interface TaskFactoryInterface
{

    /**
     * @param array $taskDescription
     * @param $type , string: install|uninstall
     * @return TaskInterface|false
     */
    public function getTask(array $taskDescription, $type);
}
