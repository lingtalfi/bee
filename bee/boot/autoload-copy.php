<?php


//------------------------------------------------------------------------------/
// AUTOLOAD SCRIPT
//------------------------------------------------------------------------------/
/**
 * Include this script to make the (bee) packages available in your php (>=5.4) application.
 */
if (!isset($_beePackagesVersions) || !is_array($_beePackagesVersions)) {
    $_beePackagesVersions = [];
}
if (!isset($_beePluginsVersions) || !is_array($_beePluginsVersions)) {
    $_beePluginsVersions = [];
}
if (!isset($_beePackagesDir)) {
    $_beePackagesDir = realpath(__DIR__ . "/..") . "/packages";
}
if (!isset($_beePluginDirs)) {
    $_beePluginDirs = realpath(__DIR__ . "/..") . "/plugins";
}

//------------------------------------------------------------------------------/
// INITIALIZING THE BEE AUTOLOADER
//------------------------------------------------------------------------------/
/**
 * It simply register ONE autoloader which will auto load bee classes.
 */
$beeVersion = (array_key_exists('Bee', $_beePackagesVersions)) ? $_beePackagesVersions['Bee'] : '_';
require_once $_beePackagesDir . "/Bee/$beeVersion/Component/Autoload/ClassAutoLoader/ClassAutoLoader.php";
require_once $_beePackagesDir . "/Bee/$beeVersion/Component/Autoload/ClassAutoLoader/BeeClassAutoLoaderTool.php";
use Bee\Bat\DebugTool;
use Bee\Component\Autoload\ClassAutoLoader\BeeClassAutoLoaderTool;
use Bee\Component\Autoload\ClassAutoLoader\ClassAutoLoader;


$_beeAutoLoader = ClassAutoLoader::getInst();
$_beeAutoLoader->register();


BeeClassAutoLoaderTool::addVersionedDirectory($_beePackagesDir, 'vendorChildren', null, $_beePackagesVersions, 100);
BeeClassAutoLoaderTool::addVersionedDirectory($_beePluginDirs, 'vendorItem', 'WebModule', $_beePluginsVersions, 100);


if (isset($_beeAppClassesDir)) {
    $_beeAutoLoader->addDirectory($_beeAppClassesDir);
}


//------------------------------------------------------------------------------/
// BONUS: HANDY FUNCTIONS
//------------------------------------------------------------------------------/
/**
 * I use those functions all the time, so since this file must be called, it's a good place for them to bee.
 */
if (!function_exists('a')) {
    function a()
    {
        foreach (func_get_args() as $arg) {
            ob_start();
            var_dump($arg);
            $output = ob_get_clean();
            if ('1' !== ini_get('xdebug.default_enable')) {
                $output = preg_replace("!\]\=\>\n(\s+)!m", "] => ", $output);
            }
            echo '<pre>' . $output . '</pre>';
        }
    }

    function az()
    {
        call_user_func_array('a', func_get_args());
        exit;
    }


    function d($msg)
    {
        $f = '/tmp/drop.txt';
        $msg .= PHP_EOL;
        file_put_contents($f, $msg, FILE_APPEND);
    }
    
    

//    function e($cmd)
//    {
//        exec($cmd, $output, $ret);
//        if (0 === $ret) {
//            return implode(PHP_EOL, $output);
//        }
//        return false;
//    }
//
//    function v($expression, $return = false)
//    {
//        if (is_array($expression)) {
//            DebugTool::arrayExport($expression, $return, [
//                'space' => ' ',
//                'eol' => PHP_EOL,
//            ]);
//        }
//        else {
//            var_export($expression, $return);
//        }
//    }
}


