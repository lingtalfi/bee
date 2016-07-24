<?php
#!/usr/bin/env php


//require_once 'alveolus/bee/boot/bam1.php';

use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use Bee\Notation\Service\Biskotte\ContainerBuilder\BiscotteContainerBuilderTool;
use Bware\SymphoBee\HttpRequest\ConfigurableHttpRequest;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter;


require_once 'alveolus/bee/boot/autoload.php';

ini_set('error_reporting', -1);
ini_set('display_errors', 1);


$services = BabyYamlTool::parseFile('logger.yml');
$params = BabyYamlTool::parseFile('params.yml');

$routes = BabyYamlTool::parseFile("routes-test.yml");
$route = $routes['route1'];




$container = BiscotteContainerBuilderTool::getHotServiceContainer($services, $params);
$router = StaticSombreroRouter::create()
    ->setControllerDirs([
        "/Volumes/Macintosh HD 2/it/web/Komin>/service création/projets/bee/developer/bee/approot0/_test/do/class",
    ])
    ->setServiceContainer($container)
    ->setSombreroArray($routes)
;
a($router->match(ConfigurableHttpRequest::create()->setUri('/hello/voo')));
