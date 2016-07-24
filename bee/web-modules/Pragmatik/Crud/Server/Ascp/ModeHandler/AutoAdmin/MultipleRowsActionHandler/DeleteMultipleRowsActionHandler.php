<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdmin\MultipleRowsActionHandler;

use Bee\Bat\ArrayTool;
use WebModule\Komin\Base\Db\Pdo\QuickPdo;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdminModeHandler;
use WebModule\Pragmatik\Crud\Util\TableInfo\Stazy\StazyTableInfoUtil;


/**
 * DeleteMultipleRowsActionHandler
 * @author Lingtalfi
 * 2015-02-13
 *
 */
class DeleteMultipleRowsActionHandler extends BaseMultipleRowsActionHandler
{
    //------------------------------------------------------------------------------/
    // IMPLEMENTS RowActionHandlerInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array: $index => bool (whether or not the entry with the $index has been deleted or not)
     *                      $index represents the index of the given $rows array.
     */
    public function execute(array $rows, $db, $table, array $crudNode, array $params, AutoAdminModeHandler $autoAdminHandler, array &$errors)
    {
        $ret = [];
        $error = false;
        if ($rows) {
            foreach ($rows as $index => $row) {
                $ret[$index] = false;
                if (false !== $riv = StazyTableInfoUtil::getInst()->getRowIdentifyingValues($db, $table, $row)) {
                    $where = [];
                    foreach ($riv as $k => $v) {
                        $where[] = [$k, '=', $v];
                    }
                    if (false !== QuickPdo::delete($db . '.' . $table, $where)) {
                        $ret[$index] = true;
                    }
                    else {
                        $error = true;
                    }
                }
                else {
                    $this->log("invalidRow", sprintf("invalid row: they don't match rif: %s for db: %s and table: %s", ArrayTool::toString($row), $db, $table), $errors);
                }
            }
        }
        if (true === $error) {
            $errors[] = "A problem occurred with the deletion of some of the records from the database";
        }
        return $ret;
    }

}
