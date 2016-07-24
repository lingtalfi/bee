<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beel\Server;

use WebModule\Komin\Base\Db\Mysql\MysqlTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Komin\Beel\ListRenderer\CrudAdminTableListRenderer;


/**
 * CrudBeelServer
 * @author Lingtalfi
 * 2015-01-10
 *
 */
class CrudBeelServer implements CrudBeelServerInterface
{

    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'max' => 20,
        ], $options);
    }

    public function deleteRows($table, array $rowsRiv)
    {
        $ret = 0;
        $rif = MysqlTool::getRowIdentifyingFields($table);
        if (false && 1 === count($rif)) {
            $vals = [];
            $col = $rif[0];
            $markers = [];
            $c = 0;
            foreach ($rowsRiv as $k => $riv) {
                if (array_key_exists($col, $riv)) {
                    $m = '_a' . $c++;
                    $vals[] = ':' . $m;
                    $markers[$m] = $riv[$col];
                }
            }
            if ($vals) {
                $glue = ' in (' . implode(', ', $vals) . ')';
            }
        }
        else {

            $rif = MysqlTool::getRowIdentifyingFields($table);
            $options = [
                'errorMode' => 0,
            ];
            foreach ($rowsRiv as $index => $riv) {
                $rriv = [];
                foreach ($riv as $k => $v) {
                    if (in_array($k, $rif, true)) {
                        $rriv[] = [$k, '=', $v];
                    }
                }
                if ($rriv) {
                    if (false !== QuickPdo::delete($table, $rriv, $options)) {
                        $ret++;
                    }
                }
            }
        }
        return $ret;
    }


    /**
     * @param string $table , a safe table name
     * @param array $params
     * @return false|array,
     *          false is returned in case of failure, and debug info should be logged
     *          In case of success, the array contains the following properties:
     *              - html: string, the html code for the table
     *              - values: array, the rows
     *
     *
     *
     */
    public function getView($table, array $params)
    {
        $primary = false;
        if (array_key_exists('primary', $params) && true === $params['primary']) {
            $primary = true;
        }


        $colNames = MysqlTool::getColumnNames($table);

        $stmt = 'select * from ' . $table;
        $sWhere = '';
        $markers = [];

        // SORTS
        //------------------------------------------------------------------------------/
        $safeSorts = [];
        $colSorts = [];
        if (array_key_exists('sorts', $params) && is_array($params['sorts'])) {
            $sorts = $params['sorts'];

            // check data against corruption
            foreach ($sorts as $sort) {
                if (is_array($sort) && 2 === count($sort)) {
                    list($colName, $dir) = $sort;
                    if (in_array($colName, $colNames, true) && ('asc' === $dir || 'desc' === $dir)) {
                        $safeSorts[] = $colName . ' ' . $dir;
                        $colSorts[$colName] = $dir;
                    }
                }
            }
        }


        // SEARCHES
        //------------------------------------------------------------------------------/
        $likeMode = (array_key_exists('likeMode', $params)) ? $params['likeMode'] : 'default'; // mysql|default
        $searchesWhere = [];
        $operators1 = ['=', '<', '>'];
        $operators2 = ['<=', '>='];
        if (array_key_exists('searches', $params) && is_array($params['searches'])) {
            $searches = $params['searches'];
            foreach ($searches as $colName => $value) {
                if (in_array($colName, $colNames, true)) {

                    $val = ltrim($value);
                    $letter1 = substr($val, 0, 1);
                    $operator = 'like';
                    if (in_array($letter1, $operators1, true)) {
                        $letter2 = substr($val, 0, 2);
                        $value = substr($val, 1);
                        if (in_array($letter2, $operators2, true)) {
                            $operator = $letter2;
                            $value = substr($val, 2);
                        }
                        else {
                            $operator = $letter1;
                        }
                    }

                    if ('like' === $operator && 'default' === $likeMode) {
                        $markers[$colName] = '%' . str_replace(['%', '_'], ['\%', '\_'], $value) . '%';
                    }
                    else {
                        $markers[$colName] = $value;
                    }
                    $searchesWhere[$colName] = $operator;
                }
            }
        }

        // COMBINE THE STMT
        //------------------------------------------------------------------------------/
        $params = array_replace([
            'max' => $this->options['max'],
            'numPage' => (array_key_exists('numPage', $params)) ? $params['numPage'] : 1,
        ], $params);
        $max = (int)$params['max'];
        $options = [
            'errorMode' => 0, // superlog
        ];
        $numPage = (int)$params['numPage'];
        if ($numPage < 1) {
            $numPage = 1;
        }
        $offset = ($numPage - 1) * $max;


        if ($searchesWhere) {
            $sWhere = ' where ';
            $c = false;
            foreach ($searchesWhere as $colName => $operator) {
                if (true === $c) {
                    $sWhere .= ' and ';
                }
                else {
                    $c = true;
                }
                $sWhere .= $colName . ' ' . $operator . ' :' . $colName;
            }
            $stmt .= $sWhere;
        }
        if ($safeSorts) {
            $stmt .= ' order by ' . implode(', ', $safeSorts);
        }
        $stmt .= ' limit ' . $offset . ', ' . $max;

        if (false !== $values = QuickPdo::fetchAll($stmt, $markers, $options)) {

            $stmt2 = "select count(*) as nbItems from " . $table;
            $stmt2 .= $sWhere;
            if (false !== $req = QuickPdo::fetch($stmt2, $markers, $options)) {
                $nbPages = ceil($req['nbItems'] / $max);
                if ($nbPages < 1) {
                    $nbPages = 1;
                }
                $o = new CrudAdminTableListRenderer(null, [
                    'colSorts' => $colSorts,
                ]);
                $o->setRegularColumns($colNames);
                $o->setSpecialColumn('actions5', 'last', '<input type="checkbox" value="1">');
                $o->setButtonsColumn('actions', 'last', ['edit', 'delete']);
                $o->setButtonsColumn('actions2', 3, ['edit', 'delete']);


                $ret = [
                    'values' => $values,
                    'stmt' => $stmt,
                    'nbPages' => $nbPages,
                ];
                if (true === $primary) {
                    $ret['html'] = $o->render($values);
                    $ret['colNames'] = $colNames;
                }
                else {
                    $ret['html'] = $o->renderBody($values);
                }
                return $ret;
            }
        }
        return false;
    }


}
