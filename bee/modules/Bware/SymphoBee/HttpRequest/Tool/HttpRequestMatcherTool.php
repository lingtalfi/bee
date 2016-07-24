<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\HttpRequest\Tool;

use Bee\Bat\StringModalMatcherTool;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;


/**
 * HttpRequestMatcherTool
 * @author Lingtalfi
 * 2015-03-10
 *
 * To perform matching test on a HttpRequest
 *
 */
class HttpRequestMatcherTool
{

    public static function matchMethod(HttpRequestInterface $request, $method)
    {
        return (strtolower($method) === strtolower($request->getMethod()));
    }

    public static function matchProtocol(HttpRequestInterface $request, $protocol)
    {
        $protocol = strtolower($protocol);
        return (
            ('http' === $protocol && false === $request->isHttps()) ||
            ('https' === $protocol && true === $request->isHttps())
        );
    }


    /**
     * Uses bee string modal matching
     */
    public static function matchHostName(HttpRequestInterface $request, $pattern, $patternMode = null, $caseSensitive = false)
    {
        return StringModalMatcherTool::match($request->getHostName(), $pattern, $patternMode, $caseSensitive);
    }

    /**
     * Uses bee string modal matching
     */
    public static function matchIp(HttpRequestInterface $request, $pattern, $patternMode = null, $caseSensitive = false)
    {
        return StringModalMatcherTool::match($request->getIp(), $pattern, $patternMode, $caseSensitive);
    }


    public static function matchPort(HttpRequestInterface $request, $port)
    {
        return ((int)$request->getPort() === (int)$port);
    }


}
