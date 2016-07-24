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

use ArachnophoBee\PhpToken\SoundRegexEngine\PreParser\PreParser;
use ArachnophoBee\PhpToken\SoundRegexEngine\SoundRegexEngine;


/**
 * DemystifierTool
 * @author Lingtalfi
 * 2015-04-09
 *
 * An alphabet is a variable assignment which right part is eval safe,
 *      like T_CONSTANT_ENCAPSED_STRING.
 *      I call those alphabets because that's how they are used by certain types of mystifiers.
 *
 *      Personal note:
 *          Basically, the main difference between applying an alphabet and replacing array references
 *          in a string is the fact that an alphabet has this security based connotation:
 *          we don't replace array references if this mean that we have to execute a code that
 *          is not eval safe: which means a code that calls arbitrary function or methods.
 *
 */
class DemystifierTool
{


    /**
     *
     *
     * @param $content
     * @return array, an array of varName => alphabets
     *                              varName contain the leading dollar symbol(s),
     *                              but not the error control operator (@) if any.
     *
     *                              varName can also be an array affectation.
     *
     */
    public static function getAlphabets($content)
    {
        return ArachnidParserTool::getVariableAssignments($content, function ($l, $r) {
            $type = $r[1][0][0];
            if (T_CONSTANT_ENCAPSED_STRING === $type) {
                return true;
            }
            return false;
        });
    }


    /**
     * Resolve array references from content using the given alphabets.
     */
    public static function applyAlphabets(array $alphabets, $content)
    {
        foreach ($alphabets as $varName => $alphabet) {
            $pat = [
                'T_VARIABLE',
                'T_WHITESPACE?',
                'T_LEFT_BRACKET',
                '(?: T_CONSTANT_ENCAPSED_STRING | T_LNUMBER )',
                'T_RIGHT_BRACKET',
            ];

            $parser = new PreParser();
            $o = new SoundRegexEngine();
            $o->setPreParser($parser);
            $content = $o->replaceCallback($pat, function ($match) use ($alphabets) {
                $value = $match[2];
                $varName = $match[2];
                if (false !== $pos = strpos($varName, '[')) {

                    $varInAlphabets = false;

                    /**
                     * An alphabet might be assigned to a simple variable or to an array:
                     *
                     *      $alphabet = "abcdef";
                     *      $GLOBALS["xpofie"] = "abcdef";
                     */
                    if (array_key_exists($varName, $alphabets)) {
                        $varInAlphabets = true;
                    }
                    else {
                        $varName = substr($varName, 0, $pos);
                        if (array_key_exists($varName, $alphabets)) {
                            $varInAlphabets = true;
                        }
                    }
                    
                    if (true === $varInAlphabets) {
                        $s = $varName . ' = ' . $alphabets[$varName] . ';';
                        $s .= ' return ' . $match[2] . ';';
                        $r = eval($s);
                        if ('\\' === $r) {
                            $value = '"\\\"';
                        }
                        else {
                            $value = '"' . str_replace('"', '\"', $r) . '"';
                        }
                    }
                }
                return $value;
            }, $content);
        }
        return $content;
    }

}
