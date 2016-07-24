<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\MysqlDumper;


/**
 * MysqlDumperInterface
 * @author Lingtalfi
 * 2015-02-14
 *
 */
interface MysqlDumperInterface
{

    /**
     * @return string|false, the mysqldump for the table creation, or false in case of failure
     */
    public function getTableDump($db, $table);
}
