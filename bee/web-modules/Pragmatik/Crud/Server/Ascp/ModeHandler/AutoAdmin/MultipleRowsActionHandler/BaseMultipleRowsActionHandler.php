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

use Bee\Component\Log\SuperLogger\SuperLogger;


/**
 * BaseMultipleRowsActionHandler
 * @author Lingtalfi
 * 2015-02-13
 *
 */
abstract class BaseMultipleRowsActionHandler implements MultipleRowsActionHandlerInterface
{

    protected function missingParam($key, array &$errors)
    {
        $errors[] = sprintf("Missing param: %s", $key);
    }


    protected function log($id, $msg, array &$errors = null)
    {
        $cName = get_called_class();
        $p = explode('\\', $cName);
        $cName = array_pop($p);
        $cName = str_replace('MultipleRowsActionHandler', '', $cName);


        $errors[] = "An error has occurred, please check the application logs";
        SuperLogger::getInst()->log("Pragmatik.Crud.Server.ModeHandler.AutoAdmin.MultipleRowsActionHandler." . $cName . '.' . $id, $msg);
    }

}
