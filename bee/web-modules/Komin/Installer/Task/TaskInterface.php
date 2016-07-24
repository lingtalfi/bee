<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Installer\Task;

use WebModule\Komin\Installer\TaskLogger\TaskLoggerInterface;


/**
 * TaskInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 */
interface TaskInterface
{

    /**
     * @return bool, true in case of success, false in case of failure
     */
    public function execute(TaskLoggerInterface $taskLogger);
}
