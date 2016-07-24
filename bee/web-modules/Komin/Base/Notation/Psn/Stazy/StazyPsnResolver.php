<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Notation\Psn\Stazy;

use Bee\Notation\String\Psn\PsnResolverInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyPsnResolver
 * @author Lingtalfi
 */
class StazyPsnResolver
{


    /**
     * @return PsnResolverInterface
     */
    public static function getInst()
    {
        return StazyContainer::getInst()->getService("komin.base.notation.psn");
    }

}
