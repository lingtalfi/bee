<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komin\Server\RemoteShell;


/**
 * RemoteShellProcessorInterface
 * @author Lingtalfi
 * 2014-10-28
 *
 */
interface RemoteShellProcessorInterface
{

    public function applyCommands($machine, $app, $commandSet);
}
