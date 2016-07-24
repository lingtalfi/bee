<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\Ascp\AjaxMasterServer\Stazy;

use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Server\Ascp\AjaxMasterServer\AjaxMasterServerInterface;


/**
 * StazyAjaxMasterServer
 * @author Lingtalfi
 */
class StazyAjaxMasterServer
{


    private static $inst;


    private function __construct()
    {

    }
    
    /**
     * @return AjaxMasterServerInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.server.ascp.ajaxMasterServer');
        }
        return self::$inst;
    }
}