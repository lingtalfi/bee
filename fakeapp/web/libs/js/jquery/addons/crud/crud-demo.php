<?php


use WebModule\Komin\Kick\Cms\Stazy\StazyKickCms;
use WebModule\Pragmatik\Crud\Util\RawScan2GsmAdaptor\RawScan2GsmAdaptor;

$_beeApplicationRoot = __DIR__;

require_once 'alveolus/bee/boot/bam1.php';


\WebModule\Komin\User\UserToken\UserTokenTool::connect([
    'login' => 'ling',
    'pass' => 'ling',
]);


$o = StazyKickCms::getInst();
echo $o->render();


