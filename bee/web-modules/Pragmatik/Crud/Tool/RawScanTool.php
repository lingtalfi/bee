<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Tool;

use WebModule\Komin\Base\Db\Mysql\MysqlTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Pragmatik\Crud\Util\MysqlDumper\MysqlDumperTool;


/**
 * RawScanTool
 * @author Lingtalfi
 * 2015-02-07
 *
 */
class RawScanTool
{

    /**
     * @return array|false in case of failure
     */
    public static function getRawScan($db, $table)
    {
        $ftable = $db . '.' . $table;
        $stmt = "show columns from " . $ftable;
        $ret = false;
        if (false !== $rs = QuickPdo::fetchAll($stmt)) {
            $ret = [];
            $fks = MysqlDumperTool::getForeignKeyInfo($db, $table);
            foreach ($rs as $r) {

                $typeLength = null;
                if (false !== $pos = strpos($r['Type'], '(')) {
                    $type = substr($r['Type'], 0, $pos);
                    $typeLength = rtrim(substr($r['Type'], $pos + 1), ')');
                } else {
                    $type = $r['Type'];
                }

                $column = $r['Field'];

                $tableInfo = [
                    'column' => $column,
                    'type' => $type,
                    'typeLength' => $typeLength,
                    'defaultValue' => $r['Default'],
                    'nullable' => $r['Null'],
                    'pk' => ('PRI' === $r['Key']),
                    'fk' => null,
                    'ai' => ('auto_increment' === $r['Extra']),
                ];

                if (array_key_exists($column, $fks)) {
                    $tableInfo['fk'] = $fks[$column];
                }
                $ret[] = $tableInfo;
            }

        }
        return $ret;

    }
}
