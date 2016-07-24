<?php

use Bee\Application\Application\BeeApp;

if (isset($_beeRootDir)) {
    require_once 'autoload.php';


    //------------------------------------------------------------------------------/
    // BEE APP SETUP
    //------------------------------------------------------------------------------/
    if (!isset($_beeEnv)) {
        // using the webserver to switch environment
        if (array_key_exists('ENVIRONMENT', $_SERVER) && 'local' === $_SERVER['ENVIRONMENT']) {
            $_beeEnv = 'dev';
            // this is just quick default setting, we might override those ini values with plugins
            ini_set("display_errors", 1);
        }
        else {
            $_beeEnv = 'prod';
            // this is just quick default setting, we might override those ini values with plugins
            ini_set("display_errors", 0);
        }
    }

    $appTags = [];
    $appTags[] = $_beeEnv;
    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        $appTags[] = 'cli';
    }
    // probably better to not use ajax tag at this level ...
//    if (
//        array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) &&
//        'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']
//    ) {
//        $appTags[] = 'ajax';
//    }
    $options = [];
    if (isset($_beeCacheDir)) {
        $options['cacheDir'] = $_beeCacheDir;
    }
    BeeApp::boot($_beeRootDir, $appTags, $options);

}
else {
    throw new \RuntimeException("You must define \$_beeRootDir variable to use this script");
}