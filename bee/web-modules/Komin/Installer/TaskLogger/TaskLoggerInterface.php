<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Installer\TaskLogger;


/**
 * TaskLoggerInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 */
interface TaskLoggerInterface
{

    public function addSuccess($msg);

    public function addError($msg);

    public function getMessages($type = null);
}
