<?php

//------------------------------------------------------------------------------/
// AJAX SERVICE FRONT END
//------------------------------------------------------------------------------/
/**
 * This script is a model that you can copy paste in your application.
 * With this one script, you can handle all application request protocol based
 * ajax services.
 */


//$_beeApplicationRoot = '...';
//$_beeAppClassesDir = '...';
require_once 'alveolus/bee/boot/booted-chopin.php';

use Komin\Server\AjaxTim\AjaxTimSession;
use Komin\Server\AjaxTim\AjaxTimSessionInterface;
use WebModule\Komin\Base\Server\AjaxService\AjaxServiceManager\Stazy\StazyAjaxServiceManager;


//------------------------------------------------------------------------------/
// INPUT
//------------------------------------------------------------------------------/
AjaxTimSession::create()->start(function (AjaxTimSessionInterface $session) {


    if (isset($_POST['serviceId'])) {
        $serviceId = $_POST['serviceId'];
        if (false !== $service = StazyAjaxServiceManager::getInst()->getAjaxService($serviceId)) {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $params = (array_key_exists('params', $_POST)) ? $_POST['params'] : [];

                if (false !== $ret = $service->execute($id, $params)) {
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



