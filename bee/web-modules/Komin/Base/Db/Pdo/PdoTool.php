<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Db\Pdo;



/**
 * PdoTool
 * @author Lingtalfi
 * 2015-02-06
 * 
 */
class PdoTool {

 
    /**
     * Returns a formatted items list.
     * 
     * 
     * @param $key, name of the column whose value will be the key of the returned array.
     *              Generally, it's id.
     *
     * @param $itemFormat, string containing tags wrapped with curly braces.
     *                  For instance: {id}. {name}
     */
    public static function getItemsFromTable($table, $key, $itemFormat)
    {
        $ret = [];
        $args = [];
        $fmt = preg_replace_callback('!\{([a-zA-Z0-9]+)\}!', function ($m) use (&$args) {
            $args[$m[1]] = true;
            return '%s';
        }, $itemFormat);
        $format = function ($row) use ($fmt, $args) {
            return vsprintf($fmt, array_replace($args, $row));
        };
        $stmt = 'select * from ' . $table;
        $rs = QuickPdo::fetchAll($stmt);
        foreach ($rs as $r) {
            $ret[$r[$key]] = $format($r);
        }
        return $ret;
    }
    
}
