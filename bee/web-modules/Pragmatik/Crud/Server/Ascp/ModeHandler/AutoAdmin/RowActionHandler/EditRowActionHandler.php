<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\RowActionHandler;

use Bee\Bat\ArrayTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdminModeHandler;
use WebModule\Pragmatik\Crud\Util\TableInfo\Stazy\StazyTableInfoUtil;


/**
 * EditRowActionHandler
 * @author Lingtalfi
 * 2015-02-10
 *
 */
class EditRowActionHandler extends BaseRowActionHandler
{
    //------------------------------------------------------------------------------/
    // IMPLEMENTS RowActionHandlerInterface
    //------------------------------------------------------------------------------/
    public function execute(array $row, $db, $table, array $crudNode, array $params, AutoAdminModeHandler $autoAdminHandler, array &$errors)
    {

        if (array_key_exists('actionPhase', $params)) {
            $actionPhase = $params['actionPhase']; // form|update
            $columns = $autoAdminHandler->getActiveColumns($crudNode, ['columns', 'columnsForm', 'columnsFormUpdate']);
            $fullTable = $db . '.' . $table;

            switch ($actionPhase) {
                case 'update':
                    if (array_key_exists('values', $params)) {
                        $newValues = $params['values'];

                        $o = $autoAdminHandler->getBeefModule($crudNode, [
                            'columns' => $columns,
                        ]);
                        /**
                         * the formId doesn't need the tail for THIS instance which uses a gsm generator,
                         * based on just the db table couple (additional info are brought by pcf).
                         */
                        $formId = $fullTable;
                        if (true !== $valErrors = $o->validate($formId, $newValues)) {
                            return [
                                '_errors' => $valErrors,
                            ];
                        }
                        else {
                            if (false !== $riv = StazyTableInfoUtil::getInst()->getRowIdentifyingValues($db, $table, $newValues)) {
                                $where = [];
                                foreach ($riv as $k => $v) {
                                    $where[] = [$k, '=', $v];
                                }
                                if (false !== $ret = QuickPdo::update($db . '.' . $table, $newValues, $where)) {
                                    return 'ok';
                                }
                                else {
                                    $errors[] = "A problem occurred with the update of the database";
                                }
                            }
                            else {
                                $this->log("invalidNewValues", sprintf("invalid new values: they don't match rif: %s for db: %s and table: %s", ArrayTool::toString($newValues), $db, $table), $errors);
                            }
                        }
                    }
                    else {
                        $this->missingParam('values', $errors);
                    }

                    break;
                default:


                    $formParams = [
                        'formId' => $fullTable,
                        /**
                         * if values is not null, we display the update form, otherwise we display the insert form
                         */
                        'values' => $row,
                    ];
                    $o = $autoAdminHandler->getBeefModule($crudNode, [
                        'columns' => $columns,
                    ]);
                    if (false !== $ret = $o->execute('getForm', $formParams)) {
                        return $ret;
                    }
                    $errors[] = implode('<br>', $o->getErrors());
                    break;
            }
        }
        else {
            $this->log('missingParamActionPhase', "missing param actionPhase", $errors);
        }

    }

}
