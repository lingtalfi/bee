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
use WebModule\Pragmatik\Crud\Util\MysqlDumper\Stazy\StazyMysqlDumper;


/**
 * MysqlDumperTool
 * @author Lingtalfi
 * 2015-02-15
 *
 */
class MysqlDumperTool
{


    /**
     * @return array|false in case of failure, in which case you might want to investigate the unix command.
     *                          If the return is an array, it's format is defined in the 
     *                          getForeignKeyInfoByMysqlDump method's comments.
     */
    public static function getForeignKeyInfo($db, $table){
        if (false !== $dump = StazyMysqlDumper::getInst()->getTableDump($db, $table)) {
            return MysqlDumperTool::getForeignKeyInfoByMysqlDump($dump, $db);
        }
        return false;
    }
    

    /**
     * @param $dump string, a dump with backtick protection
     * @param $db string, the database containing the table created in the dump.
     * @return array of foreign key => $db.$table:$key
     *                                  with:
     *                                        - db: the referenced db
     *                                        - table: the referenced table
     *                                        - key: the referenced key
     *
     */
    public static function getForeignKeyInfoByMysqlDump($dump, $db)
    {
        $ret = [];
        $pattern = '!CONSTRAINT (?:[^\s]+) FOREIGN KEY \(`([^\s]+)`\) REFERENCES `([^\s]+)` \(`([^\s]+)`\)!';
        if (preg_match_all($pattern, $dump, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $table = $match[2];
                $p = explode('.', $table, 2);
                if (2 === count($p)) {
                    $table = str_replace('`', '', $table);
                }
                else {
                    $table = $db . '.' . $p[0];
                }
                $ret[$match[1]] = $table . ':' . $match[3];
            }
        }
        return $ret;
    }
}
