<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Bee\Bat\CallableTool;
use Bee\Component\Bag\BdotBag;
use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Route\Route;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter\BaseSombreroDumper;


/**
 * SombreroDefaultRoutesDump
 * @author SombreroStaticRouter - Generator
 * 2015-06-09
 */
class SombreroDefaultRoutesDump extends BaseSombreroDumper
{

    public function testRouteRoute1 (HttpRequestInterface $r){
        //------------------------------------------------------------------------------/
        // TESTING THE REQUEST
        //------------------------------------------------------------------------------/
        // uri vars
        $vars = [];

        // checking uri                
        if (false === $vars = $this->getUriVars(
                '/doo/{word}',
                $r,
                [
                    'word' => '!^d+$!i',
                ]
            )) {
            return false;
        }

        // checking scheme
        if ('https' !== $r->scheme()) {
            return false;
        }

        // checking host
        if (false === $this->matchByString('www.tommy.com', $r->host())) {
            return false;
        }

        // checking port
        if (false === $this->matchByPattern('!12.34.[0-9]{2}.78!', (string)$r->port())) {
            return false;
        }

        // checking ip
        if (false !== $this->matchByGlob('12.34.??.78', $r->ip())) {
            return false;
        }

        // checking a post condition
        if (true === $r->post()->has('poulpe')) {
            if (false === $this->matchByString('focus', $r->post()->get('poulpe'))) {
                return false;
            }
        }
        else {
            return false;
        }

        // checking a post condition
        if (true === $r->post()->has('marie')) {
            if (false !== $this->matchByGlob('polymere', $r->post()->get('marie'))) {
                return false;
            }
        }

        // checking a post condition
        if (true === $r->post()->has('my=my')) {
            if (false === ($r->post()->get('my=my') < '6')) {
                return false;
            }
        }
        //------------------------------------------------------------------------------/
        // AT THIS POINT WE HAVE A MATCHING ROUTE
        //------------------------------------------------------------------------------/

        require_once '/Volumes/Macintosh HD 2/it/web/Komin>/service création/projets/bee/developer/bee/approot0/_test/do/class/Zombee/Controller/MyController.php';
        $reflectionClass = new \ReflectionClass('Zombee\\Controller\\MyController');
        $controller = [$reflectionClass->newInstanceArgs([
        ]), 'cry'];
        $route = Route::create()
            ->setController($controller)
            ->setMatchTest(function () { // note that this is a fake match test, obviously the matching has been done already
                return true;
            });
        $args = [
            'hello' => (array_key_exists('word', $vars)) ? $vars['word'] : null,
            'animal' => $r->post()->get('poulpe'),
            'shrek' => array (
                0 => 'fruit',
                1 => 'sport',
            ),
            'anyservice' => $this->getServiceContainer()->getService('myService'),
        ];
        // binding args
        $cArgs = $this->getControllerArgs($args, $controller);
        $route->setControllerArgs($cArgs);
        $route->setContext(BdotBag::create()->setAll([
            'doo' => 987,
            'dood' => $this->getServiceContainer()->getService('michel'),
        ]));
        return $route;
    }

    public function testRouteRoute2 (HttpRequestInterface $r){
        //------------------------------------------------------------------------------/
        // TESTING THE REQUEST
        //------------------------------------------------------------------------------/
        // uri vars
        $vars = [];

        // checking uri                
        if (false === $vars = $this->getUriVars(
                '/hello/{word}',
                $r,
                [
                ]
            )) {
            return false;
        }
        //------------------------------------------------------------------------------/
        // AT THIS POINT WE HAVE A MATCHING ROUTE
        //------------------------------------------------------------------------------/

        require_once '/Volumes/Macintosh HD 2/it/web/Komin>/service création/projets/bee/developer/bee/approot0/_test/do/class/Zombee/Controller/MyController.php';
        $reflectionClass = new \ReflectionClass('Zombee\\Controller\\MyController');
        $controller = [$reflectionClass->newInstanceArgs([
        ]), 'boo'];
        $route = Route::create()
            ->setController($controller)
            ->setMatchTest(function () { // note that this is a fake match test, obviously the matching has been done already
                return true;
            });
        $args = [
            'hello' => (array_key_exists('word', $vars)) ? $vars['word'] : null,
        ];
        // binding args
        $cArgs = $this->getControllerArgs($args, $controller);
        $route->setControllerArgs($cArgs);
        return $route;
    }

}
