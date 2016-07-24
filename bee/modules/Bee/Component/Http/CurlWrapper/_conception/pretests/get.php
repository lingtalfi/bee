<?php
#!/usr/bin/env php


//require_once 'alveolus/bee/boot/bam1.php';

use Bee\Component\Http\CurlWrapper\Connexion\CurlConnexion;
use Bee\Component\Http\CurlWrapper\Wrapper\GetCurlWrapper;
use Bee\Component\Http\HttpHeadersParser\HttpHeadersParser;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter;

require_once 'alveolus/bee/boot/autoload.php';

ini_set('error_reporting', -1);
ini_set('display_errors', 1);






$uri = 'http://httpbin.org/get';

$r = GetCurlWrapper::create()
    ->setUri($uri)
    ->setParams([
        'achmed' => 'alaoui',
    ])
    ->setCurlConnexion(CurlConnexion::create()->open())
    ->send()
;
$headers = HttpHeadersParser::create()->setRawHeaders($r->getHeaders());

a($r->getRaw());
a($headers->getApplicationType());
a($headers->getStatusCode());



