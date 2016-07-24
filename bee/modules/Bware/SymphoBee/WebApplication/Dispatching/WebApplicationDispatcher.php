<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\WebApplication\Dispatching;

use Bee\Component\Dispatching\OrderedEventDispatcher\OrderedEventDispatcher;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * WebApplicationDispatcher
 * @author Lingtalfi
 * 2015-03-09
 *
 */
class WebApplicationDispatcher extends OrderedEventDispatcher
{


    /**
     * @param array $listeners , array of eventName => pos2Listeners
     *                      With:
     *                          - pos2Listeners: array of position => listeners
     *                          - listeners: array of listener
     *                          - listener: <serviceAddress> <::> <methodName>
     */
    public function __construct(array $listeners = [])
    {
        parent::__construct();
        $container = StazyContainer::getInst();
        foreach ($listeners as $eventName => $pos2Listeners) {
            foreach ($pos2Listeners as $pos => $listeners) {
                foreach ($listeners as $listener) {
                    $p = explode('::', $listener, 2);
                    if (2 === count($p)) {
                        list($address, $method) = $p;
                        $callback = [$container->getService($address), $method];
                        $this->addListener($eventName, $callback, $pos);
                    }
                    else {
                        throw new \InvalidArgumentException(sprintf("Invalid listener string for eventName %s: %s. Missing :: symbol", $eventName, $listener));
                    }
                }
            }
        }
    }


}
