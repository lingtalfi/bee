<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\TableInfo\Stazy;

use Bee\Component\Lang\Translator\TranslatorInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Traits\Stazy\StazyTrait;
use WebModule\Pragmatik\Crud\Util\TableInfo\TableInfoUtilInterface;


/**
 * StazyTableInfoUtil
 * @author Lingtalfi
 */
class StazyTableInfoUtil
{

    use StazyTrait;

    /**
     * @return TableInfoUtilInterface
     */
    public static function getInst($allowNull = false)
    {
        return self::doGetInst('pragmatik.crud.util.tableInfo', $allowNull);
    }
}
