<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Http\Session\Stazy;


use Bee\Component\Http\Session\SessionInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazySession
 * @author Lingtalfi
 */
class StazySession
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return SessionInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            $s = StazyContainer::getInst()->getService('komin.base.http.session');
            /**
             * @var SessionInterface $s
             * In this implementation, we want to provided a started session.
             */
            $s->start();
            self::$inst = $s;
        }
        return self::$inst;
    }
}
