<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\ArachnidParser\Tools;

use ArachnophoBee\PhpToken\SoundRegexEngine\PreParser\JohnPreParser;
use ArachnophoBee\PhpToken\SoundRegexEngine\PreParser\PreParser;
use ArachnophoBee\PhpToken\SoundRegexEngine\SoundRegexEngine;
use ArachnophoBee\PhpToken\TokenFinder\VariableAssignmentTokenFinder;
use ArachnophoBee\PhpToken\Tool\TokenTool;


/**
 * ArachnidParserTool
 * @author Lingtalfi
 * 2015-04-09
 *
 */
class ArachnidParserTool
{


    /**
     * @param $content
     * @param null|callback $filterOut
     *
     *                          bool    callback ( leftPart, rightPart )
     *                                      Returns whether or not the variable should be included or
     *                                      not in the results.
     *
     *
     *
     *                                      leftPart represents is the part on the left of the equal symbol,
     *                                                  not including the error control operator (@) if any,
     *                                                  but including leading dollar symbol(s).
     *                                      rightPart represents the part on the right of the equal symbol,
     *                                                  until the ending semi-column, excluded.
     *
     *
     *                                      By default, both parts are trimmed.
     *                                                  We can change this with the options.
     *                                      Both parts are an array with the following entries:
     *                                          0: string representing the part
     *                                          1: array of tokens representing the part
     *
     *
     *
     *
     *
     *
     * @return array of varName => varValue
     *                      varName is a string which contains the leading dollar symbol(s),
     *                          but not the error control operator (@) if any.
     *
     *                      varValue is a string representing the variable value
     *
     */
    public static function getVariableAssignments($content, $filterOut = null, array $options = [])
    {

        $options = array_replace([
            'trimParts' => true,
            'allowDynamic' => true,
            'allowArrayAffectation' => true,
        ], $options);
        $ret = [];
        $tokens = token_get_all($content);
        $o = new VariableAssignmentTokenFinder();
        $o->setAllowArrayAffectation($options['allowArrayAffectation']);
        $o->setAllowDynamic($options['allowDynamic']);
        $indexes = $o->find($tokens);
        foreach ($indexes as $index) {
            $workingSlice = array_slice($tokens, $index[0], $index[1] - $index[0] + 1);
            list($leftPart, $rightPart) = TokenTool::explodeTokens('=', $workingSlice, 2);

            if (true === $options['trimParts']) {
                $leftPart = TokenTool::trim($leftPart);
                $rightPart = TokenTool::trim($rightPart);
            }
            $rightPart = TokenTool::rtrim($rightPart, [';']);
            $sLeftPart = TokenTool::tokensToString($leftPart);
            $sRightPart = TokenTool::tokensToString($rightPart);


            $skip = false;
            if (is_callable($filterOut)) {
                if (false === call_user_func($filterOut, [
                        $sLeftPart,
                        $leftPart,
                    ], [
                        $sRightPart,
                        $rightPart,
                    ])
                ) {
                    $skip = true;
                }
            }

            if (false === $skip) {
                $ret[$sLeftPart] = $sRightPart;
            }
        }

        return $ret;

    }


    public static function unconcatenateStrings($content)
    {

        $assignmentPattern = [
            'T_CONSTANT_ENCAPSED_STRING',
            'T_WHITESPACE?',
            'T_DOT',
            'T_WHITESPACE?',
            '( T_CONSTANT_ENCAPSED_STRING T_WHITESPACE? T_DOT T_WHITESPACE? )*',
            'T_CONSTANT_ENCAPSED_STRING',
        ];

        $o = new SoundRegexEngine();
        $content = $o->replaceCallback($assignmentPattern, function ($match) {
            return '"' . str_replace('"', '\"', eval('return ' . $match[2] . ';')) . '"';
        }, $content);

        return $content;
    }


}
