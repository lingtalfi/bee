<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\Module;

use WebModule\Komin\Base\Db\Mysql\MysqlTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Komin\Base\Lang\Translator\Traits\TranslatorTrait;
use WebModule\Komin\Beel\ListRenderer\CrudAdminTableListRenderer;


/**
 * AutoAdminBeelCrudModule
 * @author Lingtalfi
 * 2015-02-03
 *
 *
 * - input:
 * ----- ?rowsOnly: bool=false, to display only the tbody content, or the whole table content
 * ----- ?likeMode: string(default|mysql)=default, how does the like mode work.
 *                          If mysql is chosen, mysql jokers are available (% and _).
 *                          With default mode, search is wrapped within %% chars.
 * ----- ?maxItems: int=10, the max number of items to display
 * ----- ?numPage: int=1, the number of the page to display
 * ----- ?searches: array of $column => $searchPattern
 *                          A searchPattern can start with special operators: =, <=, >=, < and >.
 *                          I no operator is defined, like will be used.
 * ----- ?sorts: array of $sort
 *                      $sort:
 *                          0: $column
 *                          1: asc|desc
 *
 *
 * - output:
 * ----- values: array containing rows
 * ----- nbPages: int, the number of pages
 * ----- html: string, the html code of the whole table, or the tbody, depending on the input.rowsOnly option
 * ----- ?colNames: array of columnName, is only returned if input.rowsOnly is false
 *
 *
 *
 */
class AutoAdminBeelCrudModule
{

    use TranslatorTrait;

    protected $options;
    protected $table;

    public function __construct($table, array $options = [])
    {
        $this->table = $table;
        $this->options = array_replace([
            'maxItems' => 10,
            /**
             * Columns to hide from displaying, but doesn't modify the returned values
             */
            'columns' => [], // empty=no effect
            /**
             * Columns to exclude from both the displaying AND the returned values
             */
            'excludedColumns' => [],
            /**
             * array of $actionName
             */
            'rowActions' => [],
        ], $options);
    }


    public function getOutputParams(array $inputParams)
    {
        $table = $this->table;


        $rowsOnly = false;
        if (array_key_exists('rowsOnly', $inputParams) && true === $inputParams['rowsOnly']) {
            $rowsOnly = true;
        }


        $colNames = $this->getColumnNames($table);
        $this->filterExcluded($colNames);


        $stmt = 'select ' . implode(', ', $colNames) . ' from ' . $table;
        $sWhere = '';
        $markers = [];

        // SORTS
        //------------------------------------------------------------------------------/
        $safeSorts = [];
        $colSorts = [];
        if (array_key_exists('sorts', $inputParams) && is_array($inputParams['sorts'])) {
            $sorts = $inputParams['sorts'];

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
        $likeMode = (array_key_exists('likeMode', $inputParams)) ? $inputParams['likeMode'] : 'default'; // mysql|default
        $searchesWhere = [];
        $operators1 = ['=', '<', '>'];
        $operators2 = ['<=', '>='];
        if (array_key_exists('searches', $inputParams) && is_array($inputParams['searches'])) {
            $searches = $inputParams['searches'];
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
        $inputParams = array_replace([
            'maxItems' => $this->options['maxItems'],
            'numPage' => (array_key_exists('numPage', $inputParams)) ? $inputParams['numPage'] : 1,
        ], $inputParams);
        $max = (int)$inputParams['maxItems'];
        $options = [
            'errorMode' => 0, // superlog
        ];
        $numPage = (int)$inputParams['numPage'];
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

                if ($this->options['columns']) {
                    $colNames = $this->options['columns'];
                }


                // let's define the row actions
                $rowActions = $this->options['rowActions'];
                $buttons = [];
                $action2Css = [];
                foreach ($rowActions as $actionName) {
                    $buttons[$actionName] = $this->translate($actionName, $this);
                    $action2Css[$actionName] = $this->actionNameToCssClass($actionName);
                }


                $o = new CrudAdminTableListRenderer(null, [
                    'colSorts' => $colSorts,
                    'buttonAttr' => function ($name) use ($action2Css) {
                        $ret = [];
                        if (array_key_exists($name, $action2Css)) {
                            $ret['class'] = $action2Css[$name];
                        }
                        return $ret;
                    },
                ]);
                $o->setRegularColumns($colNames);
                if ($buttons) {
                    $o->setButtonsColumn('actions', 'last', $buttons);
                }


                $ret = [
                    'values' => $values,
//                    'stmt' => $stmt,
                    'nbPages' => $nbPages,
                ];
                if (false === $rowsOnly) {
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

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * We can override this method to implement a maybe more portable (more pdo than just mysql) object.
     */
    protected function getColumnNames($table)
    {
        return MysqlTool::getColumnNames($table);
    }

    /**
     * Excludes unwanted colNames (for instance confidential info like passwords)
     */
    protected function filterExcluded(array &$colNames)
    {
        $colNames = array_diff($colNames, $this->options['excludedColumns']);
    }

    protected function actionNameToCssClass($actionName)
    {
        return 'action-' . $actionName;
    }
}
