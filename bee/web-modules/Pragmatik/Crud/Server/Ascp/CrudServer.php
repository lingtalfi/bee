<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp;

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Server\Ascp\AjaxServer\AjaxServer;
use WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler\ModeHandlerInterface;
use WebModule\Pragmatik\Crud\Server\Ascp\Module\BeelCrudModule;
use Komin\User\Granter\GranterInterface;


/**
 * CrudServer
 * @author Lingtalfi
 * 2015-02-05
 *
 *
 * Some code is placed into modules, that's only for the sake of readability.
 *
 */
class CrudServer extends AjaxServer
{


    protected $modeHandlers;

    public function __construct(array $modeHandlers = [])
    {
        $this->modeHandlers = $modeHandlers;
        parent::__construct();
    }


    /**
     * @return mixed|false, false on failure, in which case errors should be set.
     *                      mixed in case of success.
     */
    public function doExecute($serviceId, array $params = [])
    {
        $ret = false;
        switch ($serviceId) {
            case 'crud':
                if (array_key_exists('mode', $params)) {
                    $mode = $params['mode'];
                    if (array_key_exists($mode, $this->modeHandlers)) {
                        $handler = $this->modeHandlers[$mode];
                        if ($handler instanceof ModeHandlerInterface) {
                            if (array_key_exists('crudId', $params)) {
                                $crudId = $params['crudId'];
                                if (false !== $ret = $handler->execute($crudId, $params)) {
                                    return $ret;
                                }
                                else {
                                    $errs = $handler->getUserErrors();
                                    if ($errs) {
                                        foreach ($errs as $err) {
                                            $this->error($err);
                                        }
                                    }
                                    else {
                                        $this->log("undefinedModeHandlerError", "An error occurred, but the modeHandler didn't set a proper error message");
                                    }
                                }
                            }
                            else {
                                $this->log("missingParamCrudId", "Missing param crudId");
                            }
                        }
                        else {
                            $this->log("invalidModeHandler", sprintf("Invalid mode handler with mode %s, instance of ModeHandlerInterface was expected", $mode));
                        }
                    }
                    else {
                        $this->log("modeHandlerNotFound", sprintf("Mode handler not found with mode: %s", $mode));
                    }
                }
                else {
                    $this->log("missingParamMode", "Missing param mode");
                }
                break;
            default:
                $this->log("serviceNotFound", sprintf("Service not found: %s", $serviceId));
                break;
        }
        return $ret;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('Pragmatik.Crud.CrudServer.' . $id, $msg);
        $this->error("An error occurred, check the application logs");
        return false;
    }
}
