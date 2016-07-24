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

use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdminModeHandler;


/**
 * RowActionHandlerInterface
 * @author Lingtalfi
 * 2015-02-10
 *
 */
interface RowActionHandlerInterface
{

    public function execute(array $row, $db, $table, array $crudNode, array $params, AutoAdminModeHandler $autoAdminHandler, array &$errors);

}
