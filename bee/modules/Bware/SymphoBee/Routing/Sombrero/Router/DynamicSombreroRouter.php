<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Router;

use ArachnophoBee\PhpToken\TokenFinder\Tool\TokenFinderTool;
use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use Bee\Bat\BglobTool;
use Bee\Bat\CallableTool;
use Bee\Component\Bag\BdotBag;
use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Route\Route;
use Bware\SymphoBee\Routing\Route\RouteInterface;
use Bware\SymphoBee\Routing\Sombrero\Exception\SombreroException;
use Bware\SymphoBee\Routing\UriMatcher\AppleUriMatcher;
use Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * DynamicSombreroRouter
 * @author Lingtalfi
 * 2015-06-02
 *
 * This router uses the sombrero notation for writing routes.
 * It suits well for development phase, or for a router with a few routes.
 *
 * It resolves routes dynamically, which is quite expensive.
 * Use StaticSombreroRouter if performance is an issue, or if you have many routes.
 *
 *
 * One advantage of DynamicSombreroRouter over StaticSombreroRouter is that
 * it accepts any callable as Controller, whereas StaticSombreroRouter does not accept callable except for static callables
 *
 *
 */
class DynamicSombreroRouter extends SombreroRouter
{

    private $array;
    /**
     * @var UriMatcherInterface
     */
    private $uriMatcher;
    private $controllerDirs;

    /**
     * @var ServiceContainerInterface
     */
    private $serviceContainer;

    public function __construct()
    {
        $this->array = [];
        $this->uriMatcher = new AppleUriMatcher();
        $this->controllerDirs = [];
    }

    public static function create()
    {
        return new static();
    }
    //------------------------------------------------------------------------------/
    // IMPLEMENTS RouterInterface
    //------------------------------------------------------------------------------/
    /**
     * Returns a matching route, or false
     *
     * @param HttpRequestInterface $r
     * @return RouteInterface|false
     */
    public function match(HttpRequestInterface $r)
    {
        foreach ($this->array as $name => $info) {
            if (array_key_exists('match', $info) && array_key_exists('controller', $info)) {
                $match = $info['match'];
                if (false !== $vars = $this->getMatchingUriVars($match, $r)) {


                    // now getting the controller callable
                    if (false !== $controller = $this->getControllerCallable($info['controller'])) {


                        $route = Route::create()
                            ->setController($controller)
                            ->setMatchTest(function () { // note that this is a fake match test, obviously the matching has been done already
                                return true;
                            });


                        // handling args
                        $args = [];
                        if (array_key_exists('args', $info)) {
                            $args = $info['args'];
                            if (is_array($args)) {
                                $this->resolveArgs($args, $r, $route, $vars);
                            }
                            else {
                                $this->syntaxError(sprintf("args property must be of type array, %s given", gettype($args)));
                            }
                        }

                        // binding args
                        $cArgs = [];
                        $cInfo = CallableTool::getParametersNameAndOptional($controller);
                        foreach ($cInfo as $cinfo) {
                            list($cArg, $isOptional) = $cinfo;
                            if (array_key_exists($cArg, $args)) {
                                $s = $args[$cArg];
                                if (is_numeric($s)) {
                                    $s = (string)$s;
                                }
                                $cArgs[$cArg] = $s;
                            }
                            else {
                                if (false === $isOptional) {
                                    $this->problem("Cannot find a corresponding value for Controller's non optional argument $cArg");
                                }
                            }
                        }
                        $route->setControllerArgs($cArgs);


                        // handling context
                        if (array_key_exists('context', $info)) {
                            $context = $info['context'];
                            if (is_array($context)) {
                                array_walk($context, function (&$v, $r, $route) {
                                    $this->prepareServiceReference($v, $r, $route);
                                });
                                $route->setContext(BdotBag::create()->setAll($context));
                            }
                            else {
                                $this->syntaxError(sprintf("context must be an array, %s given", gettype($context)));
                            }
                        }


                        return $route;
                    }
                    else {
                        $this->problem("No valid controller found. Please read sombrero documentation");
                    }
                }
            }
            else {
                $this->syntaxError("match and/or controller key missing");
            }
        }
        return false;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setSombreroArray(array $sombreroArray)
    {
        $this->array = $sombreroArray;
        return $this;
    }

    public function addControllerDir($dir)
    {
        $this->controllerDirs[] = $dir;
        return $this;
    }

    public function setControllerDirs(array $controllerDirs)
    {
        $this->controllerDirs = $controllerDirs;
        return $this;
    }

    public function setServiceContainer(ServiceContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
        return $this;
    }





    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function resolveArgs(array &$args, HttpRequestInterface $r, RouteInterface $route, array $uriContainer)
    {
        $requestContainers = [
            'post',
            'get',
            'header',
            'server',
            'cookie',
            'session',
            'file',
        ];
        array_walk($args, function (&$v) use ($r, $route, $uriContainer, $requestContainers) {
            if (is_string($v)) {
                if ('$' === substr($v, 0, 1)) {
                    $v = substr($v, 1);
                    $p = explode(':', $v, 2);
                    $container = 'uri';
                    if (2 === count($p)) {
                        $container = $p[0];
                        $v = $p[1];
                    }
                    else {
                        if (
                            'uri' === $v ||
                            in_array($v, $requestContainers, true)
                        ) {
                            $container = $v;
                            $v = null;
                        }
                    }

                    if ('uri' === $container) {
                        if (null === $v) {
                            $v = $uriContainer;
                        }
                        else {
                            if (array_key_exists($v, $uriContainer)) {
                                $v = $uriContainer[$v];
                            }
                            else {
                                $v = null;
                            }
                        }
                    }
                    elseif (in_array($container, $requestContainers)) {
                        if (null === $v) {
                            $v = $r->$container()->all();
                        }
                        else {
                            $v = $r->$container()->get($v);
                        }
                    }
                    else {
                        $this->syntaxError("Unknown request container $container");
                    }
                }
                else {
                    $this->prepareServiceReference($v, $r, $route);
                }
            }
        });
    }


    private function prepareServiceReference(&$v, HttpRequestInterface $r, RouteInterface $route)
    {
        $two = substr($v, 0, 2);
        if ('@@' === $two) {
            $v = substr($v, 2);
            if ('container' === $v) {
                if (null !== $this->serviceContainer) {
                    $v = $this->serviceContainer;
                }
                else {
                    $this->problem("This Router instance has no serviceContainer attached to it, @@container tag call is invalid");
                }
            }
            elseif ('request' === $v) {
                $v = $r;
            }
            elseif ('route' === $v) {
                $v = $route;
            }
            else {
                if (preg_match('!^([a-zA-Z0-9_.]+\+?)$!', $v, $m)) {
                    $address = $m[1];
                    $recreate = false;
                    if ('+' === substr($address, -1)) {
                        $address = substr($address, 0, -1);
                        $recreate = true;
                    }
                    $v = $this->serviceContainer->getService($address, null, $recreate);
                }
                else {
                    $this->syntaxError("Invalid tag @@$v");
                }
            }
        }
        // resolving escaping sequences
        elseif ('\$' === $two || '\@@' === substr($v, 0, 3)) {
            $v = substr($v, 1);
        }
    }

    private function getControllerCallable($controller)
    {
        if (is_callable($controller)) {
            return $controller;
        }
        else {
            $args = [];
            if (is_array($controller) && 2 === count($controller)) {
                $controller = array_merge($controller);
                list($controller, $args) = $controller;
                if (!is_array($args)) {
                    $this->syntaxError(sprintf("when the controller is of array form, the second entry represents the controller instantiation arguments and must be an array, %s given", gettype($args)));
                }
            }
            if (is_string($controller)) {
                $p = explode('->', $controller, 2);
                if (2 === count($p)) {
                    $path = $p[0];
                    $method = $p[1];
                    $path = str_replace('\\', '/', $path);

                    foreach ($this->controllerDirs as $dir) {
                        $file = $dir . "/$path.php";
                        if (file_exists($file)) {


                            require_once $file;
                            $namespace = TokenFinderTool::getNamespace(token_get_all(file_get_contents($file)));


                            $p = explode('/', $path);
                            $class = array_pop($p);
                            $class = $namespace . "\\" . $class;


                            $reflectionClass = new \ReflectionClass($class);
                            $callable = [$reflectionClass->newInstanceArgs($args), $method];
                            if (!is_callable($callable)) {
                                $this->problem("This is not a valid callable: with $controller -> $method");
                            }
                            return $callable;

                        }
                    }


                }
                else {
                    $this->syntaxError("-> operator not found in controller: $controller");
                }

            }
            else {
                $this->syntaxError(sprintf("Invalid controller notation, a string was expected, %s given", gettype($controller)));
            }
        }
        return false;
    }


    private function getMatchingUriVars($match, HttpRequestInterface $r)
    {
        if (is_string($match)) {
            if (false !== $vars = $this->getUriVars($match, $r, [])) {
                return $vars;
            }
        }
        elseif (is_array($match)) {
            $vars = [];

            //------------------------------------------------------------------------------/
            // Handling uri
            //------------------------------------------------------------------------------/
            if (array_key_exists('uri', $match)) {
                $uri = $match['uri'];
                $this->checkString($uri, 'uri');
                $requirements = (array_key_exists('requirements', $match)) ? $match['requirements'] : [];
                if (false === $vars = $this->getUriVars($uri, $r, $requirements)) {
                    return false;
                }
            }


            //------------------------------------------------------------------------------/
            // Scheme test
            //------------------------------------------------------------------------------/
            if (array_key_exists('scheme', $match)) {
                $this->checkString($match['scheme'], 'scheme');
                if ($match['scheme'] !== $r->scheme()) {
                    return false;
                }
            }


            //------------------------------------------------------------------------------/
            // Host ip port tests
            //------------------------------------------------------------------------------/
            $methods = ['host', 'port', 'ip'];
            foreach ($methods as $method) {
                if (array_key_exists($method, $match)) {
                    if (false === $this->matchString($match[$method], $r->$method(), $method)) {
                        return false;
                    }
                }
            }

            //------------------------------------------------------------------------------/
            // Containers
            //------------------------------------------------------------------------------/
            $containers = ['post', 'get', 'session', 'header', 'cookie', 'file', 'server'];
            foreach ($containers as $cont) {
                if (array_key_exists($cont, $match)) {
                    $info = $match[$cont];
                    if (is_array($info)) {
                        $this->checkArray($info, $cont);
                        if (false === $this->matchContainer($info, $cont, $r)) {
                            return false;
                        }
                    }
                    else {
                        $this->syntaxError(sprintf("The container $cont must be of type array, %s given", gettype($info)));
                    }
                }
            }


            return $vars;
        }
        else {
            $this->syntaxError(sprintf("match must be a string or an array, %s given", gettype($match)));
        }
        return false;
    }

    private function matchContainer(array $container, $containerName, HttpRequestInterface $r)
    {
        // container is one of post, get, header, server, session, file, cookie 
        // but not uri ;)
        // we can call those 7 containers the request containers for the sake of nomenclature,
        // as opposed to uri container which appears to be a route container in contrast 
        foreach ($container as $def) {
            if (is_string($def)) {

                $p = explode('=', $def, 2);
                if (2 === count($p)) {
                    // handling =, <=, >=, !=

                    $key = $p[0];
                    $ope = '=';
                    $last = substr($key, -1);
                    if (
                        '!' === $last ||
                        '<' === $last ||
                        '>' === $last
                    ) {
                        $ope = $last . '=';
                        $key = substr($key, 0, -1);
                    }
                    $isMandatory = true;
                    if ('?' === substr($key, 0, 1)) {
                        $isMandatory = false;
                        $key = substr($key, 1);
                    }

                    if (false === $this->compareValue(trim($key), $ope, trim($p[1]), $isMandatory, $containerName, $r)) {
                        return false;
                    }
                }
                else {
                    // handling <, >
                    $ope = null;
                    if (false !== $pos = strpos($def, '<')) {
                        $ope = '<';
                        $key = substr($def, 0, $pos);
                        $value = substr($def, $pos + 1);
                    }
                    elseif (false !== $pos = strpos($def, '>')) {
                        $ope = '>';
                        $key = substr($def, 0, $pos);
                        $value = substr($def, $pos + 1);
                    }
                    if (null !== $ope) {
                        $isMandatory = true;
                        if ('?' === substr($key, 0, 1)) {
                            $isMandatory = false;
                            $key = substr($key, 1);
                        }
                        if (false === $this->compareValue(trim($key), $ope, trim($value), $isMandatory, $containerName, $r)) {
                            return false;
                        }
                    }
                    else {
                        // existence - non-existence cases
                        $isPositive = true;
                        if ('!' === substr($def, 0, 1)) {
                            $isPositive = false;
                            $def = substr($def, 1);
                        }
                        if (false === $this->hasValue($def, $isPositive, $containerName, $r)) {
                            return false;
                        }
                    }
                }
            }
            elseif (is_array($def)) {
                $n = count($def);
                $def = array_merge($def);
                if (1 === $n) {
                    // negation
                    if (false === $this->hasValue($def[0], false, $containerName, $r)) {
                        return false;
                    }
                }
                elseif (3 === $n || 4 === $n) {
                    $isMandatory = true;
                    if (4 === $n) {
                        $isMandatory = $def[3];
                        if (!is_bool($isMandatory)) {
                            $this->syntaxError(sprintf("The fourth argument of a container condition as array must be a boolean, %s given", gettype($isMandatory)));
                        }
                    }
                    list($key, $ope, $value) = $def;
                    $value = (string)$value; // comparison operates only on strings
                    if (false === $this->compareValue($key, $ope, $value, $isMandatory, $containerName, $r)) {
                        return false;
                    }
                }
                else {
                    $this->syntaxError(sprintf("$container array entries of type array must container either 1, 3 or 4 entries, %s entries found", $n));
                }
            }
            else {
                $this->syntaxError(sprintf("container $containerName definitions must be of type string or array, %s given", gettype($def)));
            }
        }
        return true;
    }

    private function hasValue($key, $isPositive, $container, HttpRequestInterface $r)
    {
        $ret = false;
        if (true === $r->$container()->has($key)) {
            $ret = true;
        }
        if (false === $isPositive) {
            $ret = !$ret;
        }
        return $ret;
    }

    private function compareValue($key, $operator, $value, $isMandatory, $containerName, HttpRequestInterface $r)
    {
        $ret = false;
        if (true === $r->$containerName()->has($key)) {
            $cVal = $r->$containerName()->get($key);


            $type = 0; // 0=string; 1=glob; 2=regex

            if (is_string($value)) {
                switch ($operator) {
                    case '=':
                    case '!=':
                        $negate = false;
                        if ('!=' === $operator) {
                            $negate = true;
                        }

                        $pattern = $value;
                        $three = substr($pattern, 0, 3);
                        if ('@g:' === $three) {
                            $type = 1;
                            $pattern = substr($pattern, 3);
                        }
                        elseif ('@p:' === $three) {
                            $type = 2;
                            $pattern = substr($pattern, 3);
                        }


//                        $pattern = trim($pattern);
                        if (0 === $type) {
                            $ret = ($cVal === $pattern);
                        }
                        elseif (1 === $type) {
                            $ret = BglobTool::match($pattern, $cVal);
                        }
                        elseif (2 === $type) {
                            if (preg_match($pattern, $cVal)) {
                                $ret = true;
                            }
                            else {
                                $ret = false;
                            }
                        }

                        if (true === $negate) {
                            $ret = !$ret;
                        }

                        break;
                    case '<':
                        $ret = ($cVal < $value);
                        break;
                    case '<=':
                        $ret = ($cVal <= $value);
                        break;
                    case '>':
                        $ret = ($cVal > $value);
                        break;
                    case '>=':
                        $ret = ($cVal >= $value);
                        break;
                    default:
                        throw new SombreroException("Unknown operator: $operator");
                        break;
                }
            }
            else {
                $this->syntaxError(sprintf("value argument must be of type string, %s given", gettype($value)));
            }


        }
        else {
            if (false === $isMandatory) {
                $ret = true;
            }
        }
        return $ret;
    }

    private function matchString($pattern, $value, $method)
    {
        $type = 0; // 0=string; 1=glob; 2=regex
        $isPositive = true;


        if (is_string($pattern)) {
            if ('!' === substr($pattern, 0, 1)) {
                $isPositive = false;
                $pattern = substr($pattern, 1);
            }
            $three = substr($pattern, 0, 3);
            if ('@g:' === $three) {
                $type = 1;
                $pattern = substr($pattern, 3);
            }
            elseif ('@p:' === $three) {
                $type = 2;
                $pattern = substr($pattern, 3);
            }


            $value = (string)$value;
            if (0 === $type) {
                $ret = ($pattern === $value);
            }
            elseif (1 === $type) {
                $ret = BglobTool::match($pattern, $value);
            }
            elseif (2 === $type) {
                if (preg_match($pattern, $value)) {
                    $ret = true;
                }
                else {
                    $ret = false;
                }
            }
            if (false === $isPositive) {
                $ret = !$ret;
            }
        }
        elseif (is_array($pattern)) {
            $pattern = array_merge($pattern);
            $pat = $pattern[0];
            if (is_string($pat)) {
                $ret = ($pat === $value);
            }
            else {
                $this->syntaxError(sprintf("The value of the $method array must contain a string, %s given", gettype($pat)));
            }
        }
        else {
            $this->syntaxError(sprintf("Unknown type for $method: expected string or array, %s given", gettype($pattern)));
        }
        return $ret;
    }

    private function checkString($s, $name)
    {
        if (!is_string($s)) {
            $this->syntaxError(sprintf($name . " must be a string", gettype($s)));
        }
    }

    private function checkArray($arr, $name)
    {
        if (!is_array($arr)) {
            $this->syntaxError(sprintf($name . " must be an array, %s given", gettype($arr)));
        }
    }


    private function getUriVars($pattern, HttpRequestInterface $r, array $requirements)
    {
        if (false !== $vars = $this->uriMatcher->match($pattern, $r->uri())) {
            if ($requirements) {
                foreach ($requirements as $name => $pat) {
                    if (array_key_exists($name, $vars)) {
                        // converting null back to empty string temporarily
                        if (!preg_match($pat, (string)$vars[$name])) {
                            return false;
                        }
                    }
                    else {
                        $this->parseError("variable $name not found");
                    }
                }
            }
            return $vars;
        }
        else {
            return false;
        }
    }


}
