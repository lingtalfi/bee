<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\Ascp\AjaxMasterServer;
use WebModule\Komin\Base\Server\Ascp\AjaxMasterServer\Stazy\StazyAjaxMasterServer;
use Komin\Server\AjaxTim\AjaxTimSession;
use Komin\Server\AjaxTim\AjaxTimSessionInterface;


/**
 * AjaxMasterServerTool
 * @author Lingtalfi
 */
class AjaxMasterServerTool
{

    public static function listen()
    {
        //------------------------------------------------------------------------------/
        // INPUT
        //------------------------------------------------------------------------------/
        AjaxTimSession::create()->start(function (AjaxTimSessionInterface $session) {


            if (isset($_POST['serverId'])) {
                $serverId = $_POST['serverId'];
                if (false !== $service = StazyAjaxMasterServer::getInst()->getServer($serverId)) {
                    if (isset($_POST['id'])) {
                        $serviceId = $_POST['id'];
                        $params = (array_key_exists('params', $_POST)) ? $_POST['params'] : [];

                        if (false !== $ret = $service->execute($serviceId, $params)) {
                            $session->setSuccessData($ret);
                        } else {
                            $errors = $service->getErrors();
                            if ($errors) {
                                $error = implode("\n", $errors);
                            } else {
                                $error = "Something wrong happened with the ajax service";
                            }
                            $session->setErrorMsg($error);
                        }
                    } else {
                        $session->setErrorMsg("Invalid params, check the documentation");
                    }
                }
            } else {
                $session->setErrorMsg("Invalid params, check the doc");
            }
        })->output();
    }
}
