<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\AjaxService\AjaxServiceManager\Stazy;

use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Server\AjaxService\AjaxServiceManager\AjaxServiceManagerInterface;



/**
 * StazyAjaxServiceManager
 * @author Lingtalfi
 */
class StazyAjaxServiceManager
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return AjaxServiceManagerInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.server.ajaxService.manager');
        }
        return self::$inst;
    }
}