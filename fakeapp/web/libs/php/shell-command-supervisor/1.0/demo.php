<?php


//------------------------------------------------------------------------------/
// COMMAND SUPERVISOR by LingTalfi 2014-10-23
//------------------------------------------------------------------------------/
/**
 * Last update: 2014-10-25
 * - is now coupled with a BabyPushServer (so that we don't need to wait
 * until the end of a command to see its output).
 *
 *
 *
 */


$config = __DIR__ . '/config/conf.yml';
$serviceUrl = 'service/command-supervisor.php';
$base = null; // base href
$allCatsOpen = true;
$allDescOpen = false;
require_once __DIR__ . '/inc/gui.php';