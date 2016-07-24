<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\BeastEngine\Debugger;


/**
 * DebuggerInterface
 * @author Lingtalfi
 * 2015-01-24
 *
 * Goal of this class is to collect/give away informational debug messages
 *
 */
interface DebuggerInterface
{

    public function log($msg);

    public function getLogs();

    public function hasLogs();
}
