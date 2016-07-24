<?php


use WebModule\Komin\User\UserToken\UserTokenTool;

$_beeApplicationRoot = __DIR__;
$_beeAppClassesDir = '/Volumes/Macintosh HD/Users/pierrelafitte/Desktop/mondossier/web/Komin>/service création/projets/bee/developer/bee/lingtalfiapp/app/classes';
require_once 'alveolus/bee/boot/booted-chopin.php';

$db = 'mydb';
$table = 'product';
//$r = \WebModule\Komin\Beef\RawScan\RawScanAnalyzer::getTableInfo($db, $table);
$file = 'tmp/table.txt';
//\Bee\Notation\File\BabyYaml\Tool\BabyYamlTool::write($file, $r);


$columns = \Bee\Notation\File\BabyYaml\Tool\BabyYamlTool::parseFile($file);

$tm = \WebModule\Komin\Base\Application\SessionToken\TokenManager\Stazy\StazyTokenManager::getInst();
\WebModule\Komin\User\UserToken\Stazy\StazyUserToken::getInst()->connect([
    'login' => 'ling',
    'pass' => 'ling',
]);


$o = new \WebModule\Komin\Beef\FormControl\KominFormControlAdaptor();
foreach ($columns as $column) {
    a($o->getFormControl($column));
}


a($columns);

