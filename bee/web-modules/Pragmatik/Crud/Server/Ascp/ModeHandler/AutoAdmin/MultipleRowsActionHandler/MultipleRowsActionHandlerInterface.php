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

use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\AutoAdminModeHandler;


/**
 * MultipleRowsActionHandlerInterface
 * @author Lingtalfi
 * 2015-02-13
 *
 */
interface MultipleRowsActionHandlerInterface
{

    public function execute(array $rows, $db, $table, array $crudNode, array $params, AutoAdminModeHandler $autoAdminHandler, array &$errors);

}
