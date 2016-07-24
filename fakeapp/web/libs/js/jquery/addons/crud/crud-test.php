<?php


use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Crud\Server\MysqlCrudServer;

$_beeApplicationRoot = __DIR__;

require_once 'alveolus/bee/boot/booted-chopin.php';


\WebModule\Komin\User\UserToken\UserTokenTool::connect([
    'login' => 'ling',
    'pass' => 'ling',
]);


$id = 'c.jettmp.type_colis';
$params = [];


$o = StazyContainer::getInst()->getService('komin.crud.server');
/**
 * @var MysqlCrudServer $o
 */
$o->execute($id, $params);

