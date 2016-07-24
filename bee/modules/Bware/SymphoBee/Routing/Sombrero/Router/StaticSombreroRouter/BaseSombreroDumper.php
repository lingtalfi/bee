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

use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use Bee\Bat\BglobTool;
use Bee\Bat\CallableTool;
use Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use Bware\SymphoBee\Routing\Sombrero\Exception\SombreroException;
use Bware\SymphoBee\Routing\UriMatcher\AppleUriMatcher;
use Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * BaseSombreroDumper
 * @author Lingtalfi
 * 2015-06-03
 *
 */
class BaseSombreroDumper
{

    /**
     * @var UriMatcherInterface
     */
    private $uriMatcher;
    /**
     * @var ServiceContainerInterface
     */
    private $container;

    public function __construct(ServiceContainerInterface $container)
    {
        $this->uriMatcher = new AppleUriMatcher();
        $this->container = $container;
    }





    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getUriVars($pattern, HttpRequestInterface $r, array $requirements)
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

    protected function getControllerArgs(array $args, $controller)
    {
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
        return $cArgs;
    }


    protected function matchByString($pattern, $value)
    {
        return ($pattern === $value);
    }

    protected function matchByGlob($pattern, $value)
    {
        return BglobTool::match($pattern, $value);
    }

    protected function matchByPattern($pattern, $value)
    {
        if (preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }

    protected function getServiceContainer()
    {
        return $this->container;
    }

    /**
     * Syntax errors are user errors that we can only spot dynamically
     */
    protected function parseError($m)
    {
        $m = "Parse Error: " . $m;
        throw new SombreroException($m);
    }

    /**
     * Syntax errors are user errors that we can only spot dynamically
     */
    protected function problem($m)
    {
        $m = "Problem: " . $m;
        throw new SombreroException($m);
    }

}
