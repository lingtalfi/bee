<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\Routing\Tool;

use Bee\Bat\ArrayTool;
use Bee\Bat\StringModalMatcherTool;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use WebModule\Bware\SymphoBee\HttpRequest\Tool\HttpRequestMatcherTool;
use WebModule\Bware\SymphoBee\Routing\UriMatcher\UriMatcherInterface;


/**
 * ColombusRouteNodeMatcherTool
 * @author Lingtalfi
 * 2015-03-10
 *
 */
class ColombusRouteNodeMatcherTool
{

    private static $cpt = 0;

    /**
     * @return false|array,
     *                      the vars of the matching route
     */
    public static function match(array $routeNode, HttpRequestInterface $request, UriMatcherInterface $matcher)
    {
        $name = (array_key_exists('name', $routeNode)) ? $routeNode['name'] : 'route' . ++self::$cpt;
        if (array_key_exists('pattern', $routeNode)) {
            if (array_key_exists('controller', $routeNode)) {


                // does the uri match ?
                if (false !== $patternVars = $matcher->match($request->getPath(), $routeNode['pattern'])) {

                    /**
                     * Checking requirements.
                     * But before we do so, we need to merge the patternVars with vars, so that requirements
                     * apply on real vars values.
                     */
                    $vars = (array_key_exists('vars', $routeNode)) ? $routeNode['vars'] : [];
                    if (is_array($vars)) {
                        foreach ($patternVars as $k => $v) {
                            if (
                                null !== $v ||
                                (null === $v && false === array_key_exists($k, $vars))
                            ) {
                                $vars[$k] = $v;
                            }
                        }
                        // now checking requirements on vars.
                        if (array_key_exists('requirements', $routeNode) && is_array($routeNode['requirements'])) {
                            $isValid = true;
                            foreach ($routeNode['requirements'] as $varName => $regex) {
                                if (array_key_exists($varName, $vars)) {
                                    if (
                                        null === $vars[$varName] || // a requirement should always fail on a null value?
                                        !preg_match($regex, $vars[$varName])
                                    ) {
                                        $isValid = false;
                                        break;
                                    }
                                }
                                else {
                                    throw new \RuntimeException(sprintf("Cannot apply requirement on varName %s because it does not exist, for route %s. Available var names were %s", $varName, $name, implode(', ', array_keys($vars))));
                                }
                            }
                            if (false === $isValid) {
                                return false;
                            }
                        }


                        /**
                         * Checking other constraints
                         */
                        if (array_key_exists('constraints', $routeNode)) {
                            $constraints = $routeNode['constraints'];
                            if (
                                (array_key_exists('method', $constraints) && false === HttpRequestMatcherTool::matchMethod($request, $constraints['method'])) ||
                                (array_key_exists('protocol', $constraints) && false === HttpRequestMatcherTool::matchProtocol($request, $constraints['protocol'])) ||
                                (array_key_exists('port', $constraints) && false === HttpRequestMatcherTool::matchPort($request, $constraints['port']))
                            ) {
                                return false;
                            }
                            if (array_key_exists('hostName', $constraints)) {
                                if (false === self::checkHttpRequestProperty($constraints['hostName'], 'hostName', $request->getHostName())) {
                                    return false;
                                }
                            }
                            if (array_key_exists('ip', $constraints)) {
                                if (false === self::checkHttpRequestProperty($constraints['ip'], 'ip', $request->getIp())) {
                                    return false;
                                }
                            }
                        }


                        /**
                         * Now, the routeNode has matched the httpRequest
                         */
                        return $vars;
                    }
                    else {
                        throw new \InvalidArgumentException(sprintf("routeNode.vars must be of type array, %s given for route %s", gettype($routeNode['vars'], $name)));
                    }
                }
            }
            else {
                throw new \RuntimeException(sprintf("routeNode.controller missing for route %s", $name));
            }
        }
        else {
            throw new \RuntimeException(sprintf("routeNode.pattern missing for route %s", $name));
        }
        return false;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected static function checkHttpRequestProperty($var, $varName, $string)
    {
        if (is_string($var)) {
            if (false === StringModalMatcherTool::match($string, $var)) {
                return false;
            }
        }
        elseif (is_array($var)) {
            ArrayTool::checkKeys(['mode', 'pattern'], $var);
            $caseSensitive = (array_key_exists('caseSensitive', $var)) ? $var['caseSensitive'] : false;
            if (false === StringModalMatcherTool::match($string, $var['pattern'], $var['mode'], $caseSensitive)) {
                return false;
            }
        }
        else {
            throw new \InvalidArgumentException(sprintf("routeNode.$varName must be either an array or a string, %s given", gettype($var)));
        }
        return true;
    }

}
