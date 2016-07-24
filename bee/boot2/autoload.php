<?php


//------------------------------------------------------------------------------/
// AUTOLOAD SCRIPT
//------------------------------------------------------------------------------/
/**
 * Include this script to make the (bee) packages available in your php (>=5.4) application.
 */

require_once __DIR__ . '/az-functions.php'; // ling bonus functions
require_once __DIR__ . '/classes/BeeAutoloader.php';
require_once __DIR__ . '/classes/ButineurAutoloader.php';



ButineurAutoLoader::getInst()
->addLocation(__DIR__ . "/../modules")
->start();

