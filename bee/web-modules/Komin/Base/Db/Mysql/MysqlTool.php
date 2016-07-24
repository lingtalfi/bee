<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Db\Mysql;

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;


/**
 * MysqlTool
 * @author Lingtalfi
 * 2015-01-10
 *
 */
class MysqlTool
{

    public static function getColumnNames($table)
    {
        $ret = [];
        $cols = QuickPdo::fetchAll('show columns from ' . $table);
        foreach ($cols as $col) {
            $ret[] = $col['Field'];
        }
        return $ret;
    }

    public static function getRowIdentifyingFields($table)
    {
        $ret = [];
        $cols = QuickPdo::fetchAll('show columns from `' . $table . '`');
        foreach ($cols as $col) {
            if ('auto_increment' === $col['Extra']) {
                $ret = [$col['Field']];
                break;
            }
            $ret[] = $col['Field'];
        }
        return $ret;
    }


    /**
     * @return array|false in case of errors
     */
    public static function getTables($db)
    {
        $ret = [];
        $stmt = "show tables from `" . $db . '`';
        if (false !== $rs = QuickPdo::fetchAll($stmt)) {
            foreach ($rs as $row) {
                $ret[] = current($row);
            }
        }
        else {
            $ret = false;
        }
        return $ret;
    }

    /**
     * @return array|false in case of errors
     */
    public static function getDatabases(array $options = [])
    {
        $options = array_replace([
            'exclude' => ['information_schema', 'mysql'],
        ], $options);
        $ret = [];
        $stmt = 'show databases';
        if (false !== $rs = QuickPdo::fetchAll($stmt)) {
            foreach ($rs as $row) {
                $db = current($row);
                if (!in_array($db, $options['exclude'], true)) {
                    $ret[] = $db;
                }
            }
        }
        else {
            $ret = false;
        }
        return $ret;
    }


    public static function getForeignKeyInfo($db, $table)
    {
        $stmt = "
SELECT
    k.TABLE_SCHEMA,
    k.TABLE_NAME,
    k.COLUMN_NAME,
    i.CONSTRAINT_NAME,
    k.REFERENCED_TABLE_SCHEMA,
    k.REFERENCED_TABLE_NAME,
    k.REFERENCED_COLUMN_NAME
FROM information_schema.TABLE_CONSTRAINTS i
LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
AND i.TABLE_SCHEMA = '$db'
AND i.TABLE_NAME = '$table';
    ";
        $ret = [];
        if (false !== $rs = QuickPdo::fetchAll($stmt)) {

            // foreign key info
            foreach ($rs as $r) {
                $ret[$r['COLUMN_NAME']] = $r['REFERENCED_TABLE_SCHEMA'] . '.' . $r['REFERENCED_TABLE_NAME'] . ':' . $r['REFERENCED_COLUMN_NAME'];
            }
        }
        return $ret;
    }

}
