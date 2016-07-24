<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter;

use ArachnophoBee\PhpToken\TokenFinder\Tool\TokenFinderTool;
use Bee\Bat\ArrayTool;
use Bee\Bat\VarTool;
use Bware\SymphoBee\Routing\Sombrero\Exception\SombreroException;
use Bware\SymphoBee\Routing\Sombrero\Router\Exception\StaticSombreroRouterException;


/**
 * SombreroRouteToCodeAdaptor
 * @author Lingtalfi
 * 2015-06-03
 *
 */
class SombreroRouteToCodeAdaptor
{
    private $controllerDirs;

    public function __construct()
    {
        $this->controllerDirs = [];
    }

    public static function create()
    {
        return new static();
    }


    public function getRouteCode(array $sombreroRoute)
    {


        ArrayTool::checkKeysAndTypes([
            'match' => 'as',
            'controller' => 'sc',
        ], $sombreroRoute);

        $code = SombreroCode::create();
        $code->add(<<<TTT
            //------------------------------------------------------------------------------/
            // TESTING THE REQUEST
            //------------------------------------------------------------------------------/
TTT
        );
        $this->prepareMatchingTestCode($sombreroRoute['match'], $code);
        $code->add(<<<TTT
            //------------------------------------------------------------------------------/
            // AT THIS POINT WE HAVE A MATCHING ROUTE
            //------------------------------------------------------------------------------/
TTT
        );
        $this->prepareControllerCallable($sombreroRoute['controller'], $code);
        $code->add(<<<TTT
            \$route = Route::create()
                ->setController(\$controller)
                ->setMatchTest(function () { // note that this is a fake match test, obviously the matching has been done already
                    return true;
            });
TTT
        );

        // handling args
        $args = [];
        if (array_key_exists('args', $sombreroRoute)) {
            $args = $sombreroRoute['args'];
            if (is_array($args)) {
                $this->resolveArgs($args);
            }
            else {
                $this->syntaxError(sprintf("args property must be of type array, %s given", gettype($args)));
            }
        }
        $sArgs = $this->exportArray($args);
        $code->add(<<<TTT
            \$args = $sArgs;         
TTT
        );
        $code->add(<<<'TTT'
            // binding args
            $cArgs = $this->getControllerArgs($args, $controller);
            $route->setControllerArgs($cArgs);         
TTT
        );


        // handling context
        if (array_key_exists('context', $sombreroRoute)) {
            $context = $sombreroRoute['context'];
            if (is_array($context)) {
                array_walk($context, function (&$v) {
                    $this->prepareServiceReference($v);
                });

                $sCont = $this->exportArray($context);
                $code->add(<<<TTT
            \$route->setContext(BdotBag::create()->setAll($sCont));         
TTT
                );
            }
            else {
                $this->syntaxError(sprintf("context must be an array, %s given", gettype($context)));
            }
        }

        $code->add(<<<TTT
            return \$route;         
TTT
        );

        return $code;
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



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

    private function resolveArgs(array &$args)
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
        array_walk($args, function (&$v) use ($requestContainers) {
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
                            $v = '$vars';
                        }
                        else {
                            $sVal = var_export($v, true);
                            $v = '(array_key_exists(' . $sVal . ', $vars)) ? $vars[' . $sVal . '] : null';
                        }
                    }
                    elseif (in_array($container, $requestContainers)) {
                        if (null === $v) {
                            $v = '$r->' . $container . '()->all()';
                        }
                        else {
                            $v = '$r->' . $container . '()->get(' . var_export($v, true) . ')';
                        }
                    }
                    else {
                        $this->syntaxError("Unknown request container $container");
                    }

                    $v = PhpCode::create()->setCode($v);
                }
                else {
                    $this->prepareServiceReference($v);
                }
            }
        });
    }

    private function prepareServiceReference(&$v)
    {
        $two = substr($v, 0, 2);
        if ('@@' === $two) {
            $v = substr($v, 2);
            if ('container' === $v) {
                $v = '$this->getServiceContainer()';
            }
            elseif ('request' === $v) {
                $v = '$r';
            }
            elseif ('route' === $v) {
                $v = '$route';
            }
            else {
                if (preg_match('!^([a-zA-Z0-9_.]+\+?)$!', $v, $m)) {
                    $address = $m[1];
                    $recreate = false;
                    if ('+' === substr($address, -1)) {
                        $address = substr($address, 0, -1);
                        $recreate = true;
                    }
                    $sRecreate = var_export($recreate, true);
                    $sAddress = var_export($address, true);
                    $v = '$this->getServiceContainer()->getService(' . $sAddress . ', null, ' . $sRecreate . ')';
                }
                else {
                    $this->syntaxError("Invalid tag @@$v");
                }
            }
            $v = PhpCode::create()->setCode($v);
        }
        // resolving escaping sequences
        elseif ('\$' === $two || '\@@' === substr($v, 0, 3)) {
            $v = substr($v, 1);
        }
    }


    private function prepareControllerCallable($controller, SombreroCode $code)
    {
        if (is_callable($controller)) {
            if (
                is_string($controller) ||
                (is_array($controller) && is_string($controller[0]))
            ) {
                $sCon = var_export($controller, true);
                $code->add(<<<TTT
            \$controller = $sCon;
TTT
                );
            }
            else {
                $this->syntaxError("StaticSombreroRouter doesn't accept non static callable");
            }
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


//                            require_once $file;
                            $namespace = TokenFinderTool::getNamespace(token_get_all(file_get_contents($file)));


                            $p = explode('/', $path);
                            $class = array_pop($p);
                            $class = $namespace . "\\" . $class;
                            $sClass = var_export($class, true);
                            $sMethod = var_export($method, true);
                            $sFile = var_export($file, true);
                            $sArgs = $this->exportArray($args);


                            $code->add(<<<TTT
                            
            require_once $sFile;                            
            \$reflectionClass = new \ReflectionClass($sClass);
            \$controller = [\$reflectionClass->newInstanceArgs($sArgs), $sMethod];
TTT
                            );
                            return;
                        }
                    }

                    $this->parseError("Could not find a controller with $controller and the current controllerDirs");

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

    private function exportArray(array $array)
    {
        $arr = [];
        foreach ($array as $k => $v) {
            if ($v instanceof PhpCode) {
                $arr[$k] = $v->getCode();
            }
            else {
//                $arr[$k] = var_export($v, true);
                $arr[$k] = VarTool::varExport($v, str_repeat("\t", 2), false);
            }
        }
        $n = 2;
        $indent2 = str_repeat("\t", $n);
        $indent1 = str_repeat("\t", $n - 1) . str_repeat(" ", $n + 2);
        $s = '[' . PHP_EOL;
        foreach ($arr as $k => $v) {
            $s .= $indent2 . var_export($k, true) . " => $v," . PHP_EOL;
        }
        $s .= $indent1 . ']';
        return $s;
    }


    private function prepareMatchingTestCode($match, SombreroCode $code)
    {

        if (is_string($match)) {
            //------------------------------------------------------------------------------/
            // Handling uri
            //------------------------------------------------------------------------------/
            $match = var_export($match, true);
            $code->add(<<<TTT
            
            // checking uri
            if (false !== \$vars = \$this->getUriVars($match, \$r, [])) {
                return \$vars;
            }
TTT
            );
        }
        elseif (is_array($match)) {

            //------------------------------------------------------------------------------/
            // Handling uri
            //------------------------------------------------------------------------------/

            $code->add(<<<TTT
            // uri vars
            \$vars = [];
TTT
            );


            if (array_key_exists('uri', $match)) {
                $uri = var_export($match['uri'], true);
                $this->checkString($uri, 'uri');
                $requirements = (array_key_exists('requirements', $match)) ? $match['requirements'] : [];
                $sReq = $this->exportArray($requirements);
                $code->add(<<<TTT
                
            // checking uri                
            if (false === \$vars = \$this->getUriVars(
                    $uri,
                    \$r, 
                    $sReq
            )) {
                return false;
            }
TTT
                );
            }


            //------------------------------------------------------------------------------/
            // Scheme test
            //------------------------------------------------------------------------------/
            if (array_key_exists('scheme', $match)) {
                $this->checkString($match['scheme'], 'scheme');
                $scheme = var_export($match['scheme'], true);
                $code->add(<<<TTT
            
            // checking scheme
            if ($scheme !== \$r->scheme()) {
                return false;
            }
TTT
                );
            }

            //------------------------------------------------------------------------------/
            // Host ip port tests
            //------------------------------------------------------------------------------/
            $methods = ['host', 'port', 'ip'];
            foreach ($methods as $method) {
                if (array_key_exists($method, $match)) {
                    $this->prepareMatchString($code, $match[$method], $method);
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
                        $this->prepareMatchContainer($code, $info, $cont);

                    }
                    else {
                        $this->syntaxError(sprintf("The container $cont must be of type array, %s given", gettype($info)));
                    }
                }
            }


        }
        else {
            $this->syntaxError(sprintf("match must be a string or an array, %s given", gettype($match)));
        }


    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function prepareMatchContainer(SombreroCode $code, array $container, $containerName)
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

                    $this->prepareCompareValue(trim($key), $ope, trim($p[1]), $isMandatory, $containerName, $code);

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
                        $this->prepareCompareValue(trim($key), $ope, trim($value), $isMandatory, $containerName, $code);
                    }
                    else {
                        // existence - non-existence cases
                        $isPositive = true;
                        if ('!' === substr($def, 0, 1)) {
                            $isPositive = false;
                            $def = substr($def, 1);
                        }
                        $this->prepareHasValue($def, $isPositive, $containerName, $code);
                    }
                }
            }
            elseif (is_array($def)) {
                $n = count($def);
                $def = array_merge($def);
                if (1 === $n) {
                    // negation
                    $this->prepareHasValue($def[0], false, $containerName, $code);
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
                    $this->prepareCompareValue($key, $ope, $value, $isMandatory, $containerName, $code);
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

    private function prepareCompareValue($key, $operator, $value, $isMandatory, $containerName, SombreroCode $code)
    {


        $type = 0; // 0=string; 1=glob; 2=regex
        $sType = 'String';
        $sKey = var_export($key, true);
        $code->add(<<<TTT
                
            // checking a $containerName condition
            if (true === \$r->$containerName()->has($sKey)) {    
TTT
        );


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
                        $sType = 'Glob';
                    }
                    elseif ('@p:' === $three) {
                        $type = 2;
                        $pattern = substr($pattern, 3);
                        $sType = 'Pattern';
                    }

                    $sPat = var_export($pattern, true);
                    $sEqual = '=';
                    if (true === $negate) {
                        $sEqual = '!';
                    }


                    $code->add(<<<TTT
                if (false $sEqual== \$this->matchBy$sType($sPat, \$r->$containerName()->get($sKey))) {
                    return false;
                }
TTT
                    );


                    break;
                case '<':
                case '<=':
                case '>':
                case '>=':
                    $sValue = var_export($value, true);
                    $code->add(<<<TTT
                if (false === (\$r->$containerName()->get($sKey) $operator $sValue)) {
                    return false;
                }
TTT
                    );
                    break;
                default:
                    throw new SombreroException("Unknown operator: $operator");
                    break;
            }
        }
        else {
            $this->syntaxError(sprintf("value argument must be of type string, %s given", gettype($value)));
        }

        $code->add(<<<TTT
            }            
TTT
        );

        if (true === $isMandatory) {
            $code->add(<<<TTT
            else {
                return false;
            }                        
TTT
            );
        }

    }


    private function prepareHasValue($key, $isPositive, $container, SombreroCode $code)
    {
        $sKey = var_export($key, true);
        $sEqual = '=';
        if (false === $isPositive) {
            $sEqual = '!';
        }
        $code->add(<<<TTT
                
            // checking $container
            if (false $sEqual== \$r->$container()->has($sKey)) {
                return false;
            }
TTT
        );
    }


    private function prepareMatchString(SombreroCode $code, $pattern, $method)
    {
        $sType = 'String';
        $isPositive = true;

        // stringify the value for the case when its port   
        if ('port' === $method) {
            $sString = '(string)';
        }
        else {
            $sString = '';
        }

        if (is_string($pattern)) {
            if ('!' === substr($pattern, 0, 1)) {
                $isPositive = false;
                $pattern = substr($pattern, 1);
            }
            $three = substr($pattern, 0, 3);
            if ('@g:' === $three) {
                $pattern = substr($pattern, 3);
                $sType = 'Glob';
            }
            elseif ('@p:' === $three) {
                $pattern = substr($pattern, 3);
                $sType = 'Pattern';
            }


            $sPat = var_export($pattern, true);
            $sEqual = '=';
            if (false === $isPositive) {
                $sEqual = '!';
            }


            $code->add(<<<TTT
            
            // checking $method
            if (false $sEqual== \$this->matchBy$sType($sPat, $sString\$r->$method())) {
                return false;
            }
TTT
            );

        }
        elseif (is_array($pattern)) {
            $pattern = array_merge($pattern);
            $pat = $pattern[0];
            if (is_string($pat)) {
                $sPat = var_export($pat, true);
                $code->add(<<<TTT
                
            // checking $method                
            if (false === \$this->matchByString($sPat, $sString\$r->$method())) {
                return false;
            }
TTT
                );
            }
            else {
                $this->syntaxError(sprintf("The value of the $method array must contain a string, %s given", gettype($pat)));
            }
        }
        else {
            $this->syntaxError(sprintf("Unknown type for $method: expected string or array, %s given", gettype($pattern)));
        }
    }


    private function syntaxError($m)
    {
        $m = "Syntax Error: " . $m;
        throw new StaticSombreroRouterException($m);
    }

    private function parseError($m)
    {
        $m = "Parse Error: " . $m;
        throw new StaticSombreroRouterException($m);
    }

    private function problem($m)
    {
        $m = "Problem: " . $m;
        throw new StaticSombreroRouterException($m);
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


}
