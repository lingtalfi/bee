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

use Bee\Bat\FileSystemTool;
use WebModule\Komin\Base\Db\Mysql\MysqlTool;


/**
 * MysqlDumper
 * @author Lingtalfi
 * 2015-02-14
 *
 */
class MysqlDumper implements MysqlDumperInterface
{

    protected $mysqlDumpPath;
    protected $request;
    protected $dbUser;
    protected $dbPass;

    public function __construct(array $options = [])
    {
        $options = array_replace([
            'request' => '"$mysqldump" -u$dbUser -p$dbPass -d --compact  $db $table|sed "/ SET /d" 2>&1',
            'dbUser' => 'root',
            'dbPass' => 'root',
            'mysqlDumpPath' => '/usr/bin/mysqldump',
        ], $options);

        $this->mysqlDumpPath = $options['mysqlDumpPath'];
        $this->request = $options['request'];
        $this->dbUser = $options['dbUser'];
        $this->dbPass = $options['dbPass'];
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS MysqlDumperInterface
    //------------------------------------------------------------------------------/
    public function getTableDump($db, $table)
    {
        $cmd = $this->prepareRequest($db, $table);
        $retCmd = 0;
        ob_start();
        passthru($cmd, $retCmd);
        $ret = ob_get_clean();
        if (0 !== $retCmd) {
            $ret = false;
        }
        return $ret;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function prepareRequest($db, $table)
    {
        return str_replace([
            '$mysqldump',
            '$dbUser',
            '$dbPass',
            '$db',
            '$table',
        ], [
            $this->mysqlDumpPath,
            $this->dbUser,
            $this->dbPass,
            $db,
            $table,
        ], $this->request);
    }
}
