<?php


require_once __DIR__ . '/../boot2/az-functions.php';
require_once __DIR__ . '/../boot2/autoload.php';


ButineurAutoLoader::getInst()
    ->addLocation(__DIR__ . "/../soap", 'Soap')
    ->addLocation(__DIR__ . "/../web-modules", 'WebModule')
;