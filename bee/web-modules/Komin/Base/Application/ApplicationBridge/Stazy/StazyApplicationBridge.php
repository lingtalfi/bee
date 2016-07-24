<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge\Stazy;

use WebModule\Komin\Base\Application\ApplicationBridge\ApplicationBridgeInterface;
use WebModule\Komin\Base\Traits\Stazy\StazyTrait;


/**
 * StazyApplicationBridge
 * @author Lingtalfi
 */
class StazyApplicationBridge
{

    use StazyTrait;

    /**
     * @return ApplicationBridgeInterface
     */
    public static function getInst()
    {
        return self::doGetInst('komin.base.application.bridge');
    }
}
