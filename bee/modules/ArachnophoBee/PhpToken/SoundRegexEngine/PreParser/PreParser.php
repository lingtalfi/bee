<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\SoundRegexEngine\PreParser;

use ArachnophoBee\PhpToken\Tool\TokenTool;
use ArachnophoBee\Tool\PositionMarkerTool;


/**
 * PreParser
 * @author Lingtalfi
 * 2015-04-08
 *
 */
class PreParser implements PreParserInterface
{

    /**
     * @var array of name => callback
     *
     *                  array   callback ( array tokens )
     *                              returns the preparsed tokens array
     */
    protected $functions;
    /**
     * @var array|null,
     *              if null, all functions are active,
     *              if an array, only the functions defined in the array are active
     */
    protected $activeFunctions;


    public function __construct()
    {
        $this->functions = [];
        $this->activeFunctions = null;
    }





    //------------------------------------------------------------------------------/
    // IMPLEMENTS PreParserInterface
    //------------------------------------------------------------------------------/
    public function preparse(array $tokens)
    {

        foreach ($this->functions as $name => $fc) {
            if (is_array($this->activeFunctions && !in_array($name, $this->activeFunctions, true))) {
                continue;
            }
            $tokens = call_user_func($fc, $tokens);
        }
        return $tokens;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function getFunctions()
    {
        return $this->functions;
    }

    public function setFunctions(array $functions)
    {
        $this->functions = $functions;
    }


    public function getActiveFunctions()
    {
        return $this->activeFunctions;
    }

    public function setActiveFunctions(array $activeFunctions = null)
    {
        $this->activeFunctions = $activeFunctions;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

    /**
     * This method takes a map of sequence positions,
     *          -- which is an array of positions (startIndex, endIndex) for the relevant sequences --
     * and replaces the corresponding sequences by a symbolic token which name is also given in the parameters.
     *
     * This method handles recursion (nested sequences).
     *
     */
    public function applyMap(array $map, $tokenName, array $tokens)
    {

        PositionMarkerTool::linearToNested($map);
        $tokens = $this->doApplyMap($map, $tokenName, $tokens);
        return $tokens;
    }



    /**
     * The map here is recursive: each entry contains 3 entries:
     *          0: start pos of sequence
     *          1: end pos of sequence
     *          2: array of children
     */
    private function doApplyMap(array $map, $tokenName, array $tokens, $offset = 0)
    {
        $ret = $tokens;
        foreach ($map as $pos) {

            $pos[0] -= $offset;
            $pos[1] -= $offset;


            $anchorPos = $pos[0];
            $original = $ret[$anchorPos];

            $subTokens = array_slice($tokens, $pos[0], $pos[1] - $pos[0] + 1);
            if ($pos[2]) {
                $subTokens = $this->doApplyMap($pos[2], $tokenName, $subTokens, $pos[0] + $offset);
            }

            $ret[$anchorPos] = [
                $tokenName,
                TokenTool::tokensToString($subTokens),
                $original[2], // line number
                $subTokens,
            ];

            $anchorPos++;
            for ($i = $anchorPos; $i <= $pos[1]; $i++) {
                unset($ret[$i]);
            }


        }


        return array_merge($ret);
    }


}
