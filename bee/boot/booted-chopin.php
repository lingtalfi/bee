<?php


/**
 * If you are in a hurry, or for test purposes,
 * Call this script to have a booted chopin kernel ready.
 *
 *
 *
 */

use Bee\Application\Kernel\Kcp\Chameleon\Chopin\ChopinKernel;

if (isset($_beeApplicationRoot)) {
    //------------------------------------------------------------------------------/
    // ALVEOLUS CALL
    //------------------------------------------------------------------------------/
    if (!isset($_beeUseAppClassesDir)) {
        $_beeUseAppClassesDir = true;
    }
    if (!isset($_beeAppClassesDir)) {
        $_beeAppClassesDir = $_beeApplicationRoot . '/app/classes';
    }
    if (!isset($_beeAppConfigDir)) {
        $_beeAppConfigDir = $_beeApplicationRoot . '/app/config';
    }
    if (!isset($_beeAppCache)) {
        $_beeAppCache = $_beeApplicationRoot . '/app/cache';
    }
    if (false === $_beeUseAppClassesDir) {
        unset($_beeAppClassesDir);
    }
    require_once 'alveolus/bee/boot/autoload.php';


    //------------------------------------------------------------------------------/
    // SCRIPTS
    //------------------------------------------------------------------------------/
    if (!isset($_beeExtraParamDir)) {
        $_beeExtraParamDir = null;
    }
    if (!isset($_beeEnv)) {
        $_beeEnv = 'dev';
    }


    $kernel = ChopinKernel::create([
        'applicationRoot' => realpath($_beeApplicationRoot),
        'containerDir' => '.',
        'cache' => $_beeAppCache,
        'extraParamDir' => $_beeExtraParamDir,
    ]);
    $kernel->setConfigDir($_beeAppConfigDir)
//    ->setFilterRules(function(){})
        ->setTagSoup([$_beeEnv])
        ->boot();

    
    
}
else {
    echo "\$_beeApplicationRoot not defined";
}





