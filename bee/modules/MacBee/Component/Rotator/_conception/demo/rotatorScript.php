<?php



//------------------------------------------------------------------------------/
// THIS IS ORIGINAL ROTATOR SCRIPT FOR MAC OSX
//------------------------------------------------------------------------------/
/**
 * It uses some mac terminal capabilities, like the say command and might use the open command also.
 * Feel free to adapt to your system.
 */


use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use MacBee\Component\Rotator\Rotator;

require_once '/usr/share/bee/alveolus/bee/boot/autoload.php';



date_default_timezone_set('Europe/Paris');
ini_set('error_reporting', -1);
ini_set('display_errors', 1);

try {
    $f = __DIR__ . '/assets/rotatorConf.yml';
    Rotator::create()
        ->setConfig(BabyYamlTool::parseFile($f))
        ->rotate("stickyDatabase");
} catch (\Exception $e) {


    $logFile = '/tmp/macBeeRotator.txt';
    file_put_contents($logFile, $e->getTraceAsString(), FILE_APPEND);
    
    $dir = dirname($logFile);
//    exec('say "Master, an exception occurred, check the log file at slash tmp slash macBeeRotator dot txt" -vTing-Ting'); // yosemite has a lot of voices ;) 
    exec('say "Master, an exception occurred" -vTing-Ting'); // yosemite has a lot of voices ;) 
    exec('open  "' . $dir . '"');


}

