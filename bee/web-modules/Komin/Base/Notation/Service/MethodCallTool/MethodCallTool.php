<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Notation\Service\ServiceCallTool;

use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * MethodCallTool
 * @author Lingtalfi
 *
 *
 */
class MethodCallTool
{


    /**
     * @param $callString, one of:
     *                  - a service method call   (@myAddress.method)
     *                  - a static method call    (Foo\ClassName::method)
     */
    public static function callMethod($callString, array $args = [])
    {
        if ('@' === substr($callString, 0, 1)) {
            $callString = substr($callString, 1);
            $p = explode('.', $callString);
            $address = $p[0];
            $method = $p[1];
            return StazyContainer::getInst()->getService($address)->$method($args);
        }
        $p = explode('::', $callString);
        return call_user_func_array($p, $args);
    }


}
