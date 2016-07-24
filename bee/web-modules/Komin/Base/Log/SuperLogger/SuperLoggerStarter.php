<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Log\SuperLogger;

use Bee\Component\Log\SuperLogger\SuperLogger;


/**
 * SuperLoggerStarter
 * @author Lingtalfi
 * 2014-10-28
 *
 */
class SuperLoggerStarter implements SuperLoggerStarterInterface
{

    protected $listenersAndRules;
    protected $skippedRules;

    public function __construct(array $params = [])
    {
        $this->listenersAndRules = $params['listenersAndRules'];
        $this->skippedRules = $params['skippedRules'];
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS SuperLoggerStarterInterface
    //------------------------------------------------------------------------------/
    public function start()
    {

        $slog = SuperLogger::getInst();
        $slog->setSkippedRules($this->skippedRules);
        foreach ($this->listenersAndRules as $i) {
            if (
                array_key_exists('listener', $i) &&
                array_key_exists('rules', $i)
            ) {
                $slog->addListener($i['listener'], $i['rules']);
            }
            else {
                throw new \UnexpectedValueException("Every listenerAndRules entry should contain the two following keys: listener and rules");
            }
        }
    }
}
