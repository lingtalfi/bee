<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler;

use Bee\Component\Log\SuperLogger\SuperLogger;

use WebModule\Komin\Base\Db\Mysql\MysqlTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Komin\Beef\Server\Ascp\ControlFactory\Gsm2StaticControlFactory;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\MultipleRowsActionHandler\DeleteMultipleRowsActionHandler;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\MultipleRowsActionHandler\MultipleRowsActionHandlerInterface;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\RowActionHandler\DeleteRowActionHandler;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\RowActionHandler\EditRowActionHandler;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\RowActionHandler\RowActionHandlerInterface;
use WebModule\Pragmatik\Crud\Server\Ascp\Module\AutoAdminBeefCrudModule;
use WebModule\Pragmatik\Crud\Server\Ascp\Module\AutoAdminBeelCrudModule;
use WebModule\Pragmatik\Crud\Util\GsmGenerator\GsmGenerator;
use WebModule\Pragmatik\Crud\Util\TableInfo\Stazy\StazyTableInfoUtil;
use Komin\User\Granter\GranterInterface;


/**
 * AutoAdminModeHandler
 * @author Lingtalfi
 * 2015-02-05
 *
 */
class AutoAdminModeHandler implements ModeHandlerInterface
{


    protected $errors;
    protected $nodes;
    protected $options;

    public function __construct(array $options = [])
    {
        /**
         * For options explanations, please see the doc
         */
        $this->options = array_replace([
            'granter' => null,
            'defaultValidationFactory' => null,
            'databases' => [],
            'rowActionHandlers' => [],
            'multipleRowsActionHandlers' => [],
            'items' => [],
            'defaultDbAlias' => 'db1',
        ], $options);
        $this->errors = [];
    }


    /**
     * @return false|mixed, false in case of failure
     */
    public function execute($crudId, array $params)
    {
        if (array_key_exists('type', $params)) {
            $type = $params['type'];

            if (false !== $node = $this->getNodeInfo($crudId)) {

                if (true === $this->isGrantedNodeAccess($node)) {


                    /**
                     * Should check the table name maybe?
                     */
                    $db = $node['_db'];
                    $table = $node['_table'];
                    $fullTable = $db . '.' . $table;
                    $actions = (array_key_exists('actions', $node)) ? $node['actions'] : null;
                    if (null === $actions) {
                        $actions = ['create', 'read'];
                    }
                    $rowActions = (array_key_exists('rowActions', $node)) ? $node['rowActions'] : null;
                    if (null === $rowActions) {
                        $rowActions = ['edit', 'delete'];
                    }


                    switch ($type) {
                        case 'list':
                            if (in_array('read', $actions)) {
                                $excludedColumns = (array_key_exists('excludedColumns', $node)) ? $node['excludedColumns'] : [];
                                $columns = $this->getActiveColumns($node, ['columns', 'columnsList']);
                                $o = new AutoAdminBeelCrudModule($fullTable, [
                                    'excludedColumns' => $excludedColumns,
                                    'columns' => $columns,
                                    'rowActions' => $rowActions,
                                ]);
                                return $o->getOutputParams($params);
                            }
                            else {
                                $this->error(sprintf("You don't have the permission to display the list of items (%s)", $crudId));
                            }

                            break;
                        case 'formInsert':
                            if (in_array('create', $actions)) {
                                $columns = $this->getActiveColumns($node, ['columns', 'columnsForm', 'columnsFormInsert']);
                                $formParams = [
                                    'formId' => $fullTable,
                                    /**
                                     * if values is not null, we display the update form, otherwise we display the insert form
                                     */
                                    'values' => null,
                                ];
                                $o = $this->getBeefModule($node, [
                                    'columns' => $columns,
                                ]);
                                if (false !== $ret = $o->execute('getForm', $formParams)) {
                                    return $ret;
                                }
                                $this->error($o->getErrors());
                            }
                            else {
                                $this->error(sprintf("You don't have the permission to display the insert form (%s)", $crudId));
                            }
                            break;
                        case 'insert':
                            if (in_array('create', $actions)) {
                                $columns = $this->getActiveColumns($node, ['columns', 'columnsForm', 'columnsFormInsert']);
                                $o = $this->getBeefModule($node, [
                                    'columns' => $columns,
                                ]);

                                if (array_key_exists('values', $params)) {
                                    $vals = $params['values'];

                                    /**
                                     * Preparing values according to the columns and defaultColumnValues
                                     */
                                    $values = [];
                                    $defaultColumnsValues = (array_key_exists('defaultColumnsValues', $node)) ? $node['defaultColumnsValues'] : [];
                                    $tableColumns = $this->getColumnNames($fullTable);
                                    foreach ($tableColumns as $k) {
                                        if (array_key_exists($k, $vals)) {
                                            $values[$k] = $vals[$k];
                                        }
                                        elseif (array_key_exists($k, $defaultColumnsValues)) {
                                            $values[$k] = $defaultColumnsValues[$k];
                                        }
                                    }


                                    /**
                                     * the formId doesn't need the tail for THIS instance which uses a gsm generator,
                                     * based on just the db table couple (additional info are brought by pcf).
                                     */
                                    $formId = $fullTable;
                                    if (true !== $errors = $o->validate($formId, $values)) {
                                        return [
                                            '_errors' => $errors,
                                        ];
                                    }
                                    else {
                                        if (false !== $row = $this->insertValues($db, $table, $values)) {
                                            return $row;
                                        }
                                        else {
                                            $this->error("A problem occurred with insertion, check the application logs");
                                            return false;
                                        }
                                    }
                                }
                                else {
                                    $this->error("Some params are missing, check the doc");
                                }
                            }
                            else {
                                $this->error(sprintf("You don't have the permission to insert data (%s)", $crudId));
                            }
                            break;
                        case 'rowAction':
                            if (array_key_exists('actionName', $params)) {
                                if (array_key_exists('rowValues', $params)) {
                                    $actionName = $params['actionName'];
                                    $row = $params['rowValues'];


                                    $rowActionHandlers = (array_key_exists('rowActionHandlers', $this->options)) ? $this->options['rowActionHandlers'] : null;
                                    if (null === $rowActionHandlers) {
                                        $rowActionHandlers = [
                                            'edit' => new EditRowActionHandler(),
                                            'delete' => new DeleteRowActionHandler(),
                                        ];
                                    }

                                    if (in_array($actionName, $rowActions, true)) {
                                        if (array_key_exists($actionName, $rowActionHandlers)) {
                                            $actionHandler = $rowActionHandlers[$actionName];
                                            if ($actionHandler instanceof RowActionHandlerInterface) {
                                                $errors = [];
                                                $ret = $actionHandler->execute($row, $db, $table, $node, $params, $this, $errors);
                                                if ($errors) {
                                                    $this->error($errors);
                                                }
                                                else {
                                                    return $ret;
                                                }
                                            }
                                            else {
                                                $this->log("invalidRowActionHandler", sprintf("Invalid row action handler: %s with crudId: %s", $actionName, $crudId));
                                            }
                                        }
                                        else {
                                            $this->log("undefinedRowActionHandler", sprintf("Undefined row action handler: %s with crudId: %s", $actionName, $crudId));
                                        }
                                    }
                                    else {
                                        $this->error(sprintf("This action is unavailable (%s)", $crudId));
                                    }
                                }
                                else {
                                    $this->log("missingParamRowValues", "Missing the param rowValues");
                                }
                            }
                            else {
                                $this->log("missingParamActionName", "Missing the param actionName");
                            }
                            break;
                        case 'multipleRowsAction':
                            if (array_key_exists('actionName', $params)) {
                                if (array_key_exists('rows', $params)) {


                                    $multipleRowActions = (array_key_exists('multipleRowActions', $node)) ? $node['multipleRowActions'] : null;
                                    if (null === $multipleRowActions) {
                                        $multipleRowActions = ['delete'];
                                    }

                                    $actionName = $params['actionName'];
                                    $rows = $params['rows'];


                                    $multipleRowsActionHandlers = (array_key_exists('multipleRowsActionHandlers', $this->options)) ? $this->options['multipleRowsActionHandlers'] : null;
                                    if (null === $multipleRowsActionHandlers) {
                                        $multipleRowsActionHandlers = [
                                            'delete' => new DeleteMultipleRowsActionHandler(),
                                        ];
                                    }

                                    if (in_array($actionName, $multipleRowActions, true)) {
                                        if (array_key_exists($actionName, $multipleRowsActionHandlers)) {
                                            $actionHandler = $multipleRowsActionHandlers[$actionName];
                                            if ($actionHandler instanceof MultipleRowsActionHandlerInterface) {
                                                $errors = [];
                                                $ret = $actionHandler->execute($rows, $db, $table, $node, $params, $this, $errors);
                                                if ($errors) {
                                                    $this->error($errors);
                                                }
                                                else {
                                                    return $ret;
                                                }
                                            }
                                            else {
                                                $this->log("invalidMultipleRowsActionHandler", sprintf("Invalid multiple rows action handler: %s with crudId: %s", $actionName, $crudId));
                                            }
                                        }
                                        else {
                                            $this->log("undefinedRowActionHandler", sprintf("Undefined row action handler: %s with crudId: %s", $actionName, $crudId));
                                        }
                                    }
                                    else {
                                        $this->error(sprintf("This multiple action is unavailable (%s)", $crudId));
                                    }
                                }
                                else {
                                    $this->log("missingParamRows", "Missing the param rows");
                                }
                            }
                            else {
                                $this->log("missingParamActionName", "Missing the param actionName");
                            }
                            break;
                        default:
                            $this->log("unknownCrudType", sprintf("Unknown crud type: %s", $type));
                            break;
                    }

                }
                else {
                    $this->error(sprintf("Unauthorized: you don't have enough permissions to execute this action (%s)", $crudId));
                }

            }
        }
        else {
            $this->log("missingParamType", "Missing the param type");
        }
        return false;
    }


    public function getUserErrors()
    {
        return $this->errors;
    }

    public function getActiveColumns(array $node, array $columnsToMerge)
    {
        $ret = null;
        foreach ($columnsToMerge as $col) {
            if (array_key_exists($col, $node) && is_array($node[$col])) {
                $ret = $node[$col];
            }
        }
        return $ret;
    }

    public function getColumnNames($fullTable)
    {
        return MysqlTool::getColumnNames($fullTable);
    }

    /**
     * @return AutoAdminBeefCrudModule
     */
    public function getBeefModule(array $crudNode, array $options = [])
    {
        $options = array_replace([
            'columns' => null,
        ], $options);
        $columns = $options['columns'];
        if (null === $columns) {
            $columns = $this->getColumnNames($crudNode['_db'] . '.' . $crudNode['_table']);
        }
        $gen = new GsmGenerator();

        $labels = (array_key_exists('labels', $crudNode)) ? $crudNode['labels'] : [];
        $tips = (array_key_exists('tips', $crudNode)) ? $crudNode['tips'] : [];

        $validationTestServer = null;
        if (array_key_exists('validationTestServer', $crudNode)) {
            $validationTestServer = $crudNode['validationTestServer'];
        }
        elseif (array_key_exists('defaultValidationTestServer', $this->options)) {
            $validationTestServer = $this->options['validationTestServer'];
        }


        return new AutoAdminBeefCrudModule($crudNode, $gen, [
            'controlWrapTag' => null, // using table, so we don't need the default wrappers
            'htmlWrap' => function ($h) { // using table, so we need to wrap all trs within a table
                return '<table>' . $h . '</table>';
            },
            'validationTestServer' => $validationTestServer,
            'controlFactories' => [
                new Gsm2StaticControlFactory(),
            ],
            'nodesFilter' => function (array $nodes) use ($columns, $labels, $tips) {
                $ret = [];
                foreach ($columns as $cName) {
                    $node = $nodes[$cName];
                    if (array_key_exists($cName, $labels)) {
                        $node['label'] = $labels[$cName];
                    }
                    if (array_key_exists($cName, $tips)) {
                        $node['tip'] = $tips[$cName];
                    }
                    $ret[$cName] = $node;
                }
                return $ret;
            }
        ]);
    }
    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return array|false in case of failure, or array of inserted values otherwise
     */
    protected function insertValues($db, $table, array $values)
    {
        $scan = $this->getRawScan($db, $table);
        $firstAiColumn = null;
        foreach ($scan as $r) {
            if (true === $r['ai'] && array_key_exists($r['column'], $values)) {
                $values[$r['column']] = null;
                $firstAiColumn = $r['column'];
            }
        }

        $ftable = $db . '.' . $table;
        if (false !== $id = QuickPdo::insert($ftable, $values)
        ) {
            if (null !== $firstAiColumn) {
                $values[$firstAiColumn] = $id;
            }
            return $values;
        }
        return false;
    }

    protected function getRawScan($db, $table)
    {
        return StazyTableInfoUtil::getInst()->getRawScan($db, $table);
    }


    protected function getNodeInfo($crudId)
    {
        $ret = false;
        $items = $this->options['items'];
        $found = false;
        // darkening technique
        if (false === strpos($crudId, '.')) {
            if (array_key_exists($crudId, $items)) {
                $node = $items[$crudId];
                if (
                    array_key_exists('db', $node) &&
                    array_key_exists('table', $node)
                ) {
                    $db = $node['db'];
                    if (false !== $realDb = $this->resolveDb($db)) {
                        $db = $realDb;
                    }
                    $table = $node['table'];
                    $tail = null;
                    $found = true;
                }
                else {
                    $this->log('undefinedDbAndTable', sprintf("undefined db and table for dark item with crudId: %s", $crudId));
                }
            }
            else {
                $this->log('darkItemNotFound', sprintf("dark item not found with crudId: %s", $crudId));
            }
        }
        // transparent technique
        else {
            $segments = explode('.', $crudId, 2);
            if (2 === count($segments)) {
                $db = array_shift($segments);
                $table = array_shift($segments);
            }
            else {
                $db = $this->options['defaultDbAlias'];
                $table = array_shift($segments);
            }
            $p2 = explode('/', $table, 2);
            $tail = 'default';
            if (2 === count($p2)) {
                $table = $p2[0];
                $tail = $p2[1];
            }

            if (false !== $realDb = $this->resolveDb($db)) {
                $db = $realDb;
            }
            $crudIdSegs = [$db, $table, $tail];
            foreach ($items as $pattern => $node) {
                if (true === $this->crudPatternMatch($pattern, $crudIdSegs)) {
                    $found = true;
                    break;
                }
            }
            if (false === $found) {
                $this->log("transparentItemNotFound", sprintf("transparent item not found with crudId: %s", $crudId));
            }
        }

        if (true === $found) {
            $node['_db'] = $db;
            $node['_table'] = $table;
            $node['_tail'] = $tail;
            $ret = $node;
        }
        return $ret;
    }

    protected function resolveDb($dbOrAlias)
    {
        $dbs = $this->options['databases'];
        if (is_array($dbs)) {
            foreach ($dbs as $real => $aliases) {
                if (!is_array($aliases)) {
                    $aliases = [$aliases];
                }
                if (in_array($dbOrAlias, $aliases, true)) {
                    $dbOrAlias = $real;
                    break;
                }
            }
        }
        return $dbOrAlias;
    }

    protected function isGrantedNodeAccess(array $node)
    {
        $ret = true;
        if (array_key_exists('badges', $node)) {
            $badges = $node['badges'];
            if (!empty($badges)) {
                $granter = $this->options['granter'];
                if ($granter instanceof GranterInterface) {
                    $ret = $granter->isGranted($badges);
                }
                else {
                    // by default, a non empty badge is never granted if there
                    // is no granting mechanism available
                    $ret = false;
                }
            }
        }
        return $ret;
    }

    protected function crudPatternMatch($pattern, array $crudIdSegments)
    {
        // pattern has always 2 dot separated components
        $p = explode('.', $pattern, 2);
        list($db, $table, $tail) = $crudIdSegments;

        $pdb = array_shift($p);
        if (false !== $realDb = $this->resolveDb($pdb)) {
            $pdb = $realDb;
        }
        $ptable = array_shift($p);
        $p2 = explode('/', $ptable);
        if (2 === count($p2)) {
            $ptable = $p2[0];
            $ptail = $p2[1];
            if ('*' === $pdb || $pdb === $db) { // comparing real db to real db
                if ('*' === $ptable || $ptable === $table) {
                    if ('*' === $ptail || $ptail === $tail) {
                        return true;
                    }
                }
            }
        }
        else {
            $this->log("tailNotFound", sprintf("Invalid crud id pattern, tail was not found in table component: %s", $ptable));
        }
        return false;
    }


    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('Pragmatik.Crud.Server.ModeHandler.' . $id, $msg);
        $this->error("An error occurred, check the application logs");
        return false;
    }


    protected function error($msg)
    {
        if (is_array($msg)) {
            $msg = implode('<br>', $msg);
        }
        $msg = 'Crud: AutoAdminModeHandler: ' . $msg;
        $this->errors[] = $msg;
        return false;
    }
}
