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
 * Debugger
 * @author Lingtalfi
 * 2015-01-24
 *
 */
class Debugger implements DebuggerInterface
{

    protected $logs;

    public function __construct()
    {
        $this->logs = [];
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS DebuggerInterface
    //------------------------------------------------------------------------------/
    public function log($msg)
    {
        $this->logs[] = $msg;
    }

    public function getLogs()
    {
        return $this->logs;
    }

    public function hasLogs()
    {
        return (!empty($this->logs));
    }
}
