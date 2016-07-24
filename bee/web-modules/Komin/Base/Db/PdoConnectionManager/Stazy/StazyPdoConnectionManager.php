<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Db\PdoConnectionManager\Stazy;
use Bee\Component\Db\PdoConnectionManager\PdoConnectionManagerInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyPdoConnectionManager
 * @author Lingtalfi
 * 2014-10-13
 * 
 */
class StazyPdoConnectionManager {

    private static $inst;

    private function __construct()
    {
    }

    /**
     * @return PdoConnectionManagerInterface
     */
    public static function getInst(){
        if(null===self::$inst){
            self::$inst = StazyContainer::getInst()->getService('komin.base.db.pdoConnectionManager');
        }
        return self::$inst;
    }



}
