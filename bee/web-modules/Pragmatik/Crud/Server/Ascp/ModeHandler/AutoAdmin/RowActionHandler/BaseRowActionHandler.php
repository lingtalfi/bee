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

use Bee\Component\Log\SuperLogger\SuperLogger;


/**
 * BaseRowActionHandler
 * @author Lingtalfi
 * 2015-02-10
 *
 */
abstract class BaseRowActionHandler implements RowActionHandlerInterface
{

    protected function missingParam($key, array &$errors)
    {
        $errors[] = sprintf("Missing param: %s", $key);
    }


    protected function log($id, $msg, array &$errors=null)
    {
        $cName = get_called_class();
        $p = explode('\\', $cName);
        $cName = array_pop($p);
        $cName = str_replace('RowActionHandler', '', $cName);

        
        $errors[] = "An error has occurred, please check the application logs";
        SuperLogger::getInst()->log("Pragmatik.Crud.Server.ModeHandler.AutoAdmin.RowActionHandler." . $cName . '.' . $id, $msg);
    }

}
