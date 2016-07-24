<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\UriMatcher;


/**
 * AppleUriMatcher
 * @author Lingtalfi
 * 2015-03-10
 *
 * The notation for the pattern used by this class is:
 *
 *      apple uri matching notation
 *
 */
class AppleUriMatcherOld implements UriMatcherInterface
{

    protected $useWildCard;

    public function __construct()
    {
        $this->useWildCard = false;
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS UriMatcherInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array|false
     */
    public function match($uri, $pattern)
    {
        $ret = false;
        $patternVars = [];
        $slashTagVars = [];
        $wildCardVars = [];


        // remove last slash if any
        if ('/' === substr($uri, -1)) {
            $uri = substr($uri, 0, -1);
        }
        if (empty($uri)) {
            $uri = '/';
        }


        // extract vars from pattern
        if (preg_match_all('#(?<!\\\)\{[/*]?[a-zA-Z0-9_]+(?<!\\\)\}#', $pattern, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $index => $match) {
                $inner = substr($match[0], 1, -1);
                $f = substr($inner, 0, 1);
                if ('/' === $f) {
                    $inner = substr($inner, 1);
                    $slashTagVars[] = $inner;
                } elseif (true === $this->useWildCard && '*' === $f) {
                    $inner = substr($inner, 1);
                    $wildCardVars[] = $inner;
                }
                $patternVars[$index] = $inner;
            }
        }

        // convert to regex
        $regex = '!^' . preg_quote($pattern, '!') . '$!';
        // if pattern used \{, it will contains two escape chars (because of preg_quote).
        $regex = str_replace([
            '\\\\{',
            '\\\\}',
            '\\{',
            '\\}',
            '\\*',
        ], [
            '{',
            '}',
            '{',
            '}',
            '*',
        ], $regex);


        if ($patternVars) {
            foreach ($patternVars as $varName) {
                if (false === in_array($varName, $slashTagVars, true)) {
                    if (true === $this->useWildCard) {
                        if (false === in_array($varName, $wildCardVars, true)) {
                            $regex = str_replace('{' . $varName . '}', '([^/]*+)', $regex);
                        } else {
                            $regex = str_replace('{*' . $varName . '}', '([\s\S]*+)', $regex);
                        }
                    } else {
                        $regex = str_replace('{' . $varName . '}', '([^/]*+)', $regex);
                    }
                } else {
                    $regex = str_replace('{/' . $varName . '}', '(?:/?([^/]*+))', $regex);
                }
            }
        }


        // perform the matching
        if (preg_match($regex, $uri, $matches)) {
            $ret = array();
            array_shift($matches); // drop the first key (whole match) to synchronize the matching with patternVars


            foreach ($matches as $i => $v) {
                if (array_key_exists($i, $patternVars)) {
                    // remove the leading slash from matching slash tag
                    if (true === in_array($i, $slashTagVars, true)) {
                        $v = substr($v, 1);
                    }
                    if ('' === $v) {
                        $v = null;
                    }

                    $ret[$patternVars[$i]] = $v;
                }
            }
        }
        return $ret;
    }
}
