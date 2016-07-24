<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\MysqlDumper\Stazy;

use Bee\Component\Lang\Translator\TranslatorInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Traits\Stazy\StazyTrait;
use WebModule\Pragmatik\Crud\Util\MysqlDumper\MysqlDumperInterface;
use WebModule\Pragmatik\Crud\Util\TableInfo\TableInfoUtilInterface;


/**
 * StazyMysqlDumper
 * @author Lingtalfi
 */
class StazyMysqlDumper
{

    use StazyTrait;

    /**
     * @return MysqlDumperInterface
     */
    public static function getInst()
    {
        return self::doGetInst('pragmatik.crud.util.mysqlDumper');
    }
}
