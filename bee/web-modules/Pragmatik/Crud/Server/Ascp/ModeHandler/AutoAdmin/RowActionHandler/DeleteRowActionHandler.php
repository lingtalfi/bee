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
 * DeleteRowActionHandler
 * @author Lingtalfi
 * 2015-02-10
 *
 */
class DeleteRowActionHandler extends BaseRowActionHandler
{
    //------------------------------------------------------------------------------/
    // IMPLEMENTS RowActionHandlerInterface
    //------------------------------------------------------------------------------/
    public function execute(array $row, $db, $table, array $crudNode, array $params, AutoAdminModeHandler $autoAdminHandler, array &$errors)
    {
        if (false !== $riv = StazyTableInfoUtil::getInst()->getRowIdentifyingValues($db, $table, $row)) {
            $where = [];
            foreach ($riv as $k => $v) {
                $where[] = [$k, '=', $v];
            }
            if (false !== $int = QuickPdo::delete($db . '.' . $table, $where)) {
                return 'ok';
            }
            else {
                $errors[] = "A problem occurred with the deletion of the record from the database";
            }
        }
        else {
            $this->log("invalidNewValues", sprintf("invalid row: they don't match rif: %s for db: %s and table: %s", ArrayTool::toString($row), $db, $table), $errors);
        }
    }

}
