<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\BeastEngine;
use WebModule\Komin\Beast\BeastEngine\Debugger\DebuggerInterface;


/**
 * DebuggableBeastEngineInterface
 * @author Lingtalfi
 * 2015-01-24
 *
 */
interface DebuggableBeastEngineInterface extends BeastEngineInterface
{
    /**
     * @return DebuggerInterface
     */
    public function getDebugger();
}
