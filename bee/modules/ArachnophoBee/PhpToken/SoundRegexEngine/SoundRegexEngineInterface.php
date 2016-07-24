<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\SoundRegexEngine;

use ArachnophoBee\PhpToken\SoundRegexEngine\PreParser\PreParserInterface;


/**
 * SoundRegexEngineInterface
 * @author Lingtalfi
 * 2015-04-07
 *
 *
 * With sound regex, we can use the full potential of php regex to match tokens.
 * A sound regex is simply a pattern made with space separated php token names.
 * Each token name is considered as one regex char (i.e., we can add regex magic to them).
 * For instance:
 *
 *      T_VARIABLE T_WHITESPACE?  T_EQUALS  T_WHITESPACE? ( T_CONSTANT_ENCAPSED_STRING | T_LNUMBER )   T_SEMI_COLON
 *
 *      This pattern would match a variable assignment with the right operand being either a quoted string or an int.
 *
 *      Extra spaces can be added for clarity.
 *
 *      The default php constants for token are used, and the following subset is used for the other chars that php represent with a string:
 *
 *
 * - (    =  T_LEFT_PARENTHESIS
 * - )    =  T_RIGHT_PARENTHESIS
 * - [    =  T_LEFT_BRACKET
 * - ]    =  T_RIGHT_BRACKET
 * - {    =  T_LEFT_CURLY_BRACKET
 * - }    =  T_RIGHT_CURLY_BRACKET
 * - .    =  T_DOT
 * - ;    =  T_SEMI_COLON
 * - ,    =  T_COMMA
 * - %    =  T_PERCENT
 * - /    =  T_FORWARD_SLASH
 * - -    =  T_DASH
 * - +    =  T_PLUS
 * - *    =  T_STAR
 * - =    =  T_EQUALS
 * - $    =  T_DOLLAR
 * - @    =  T_AROBASE
 * - <    =  T_LOWER_THAN
 * - >    =  T_GREATER_THAN
 * - &    =  T_AMPERSAND
 * - !    =  T_EXCLAMATION_MARK
 * - ?    =  T_QUESTION_MARK
 * - :    =  T_COLON
 * - ^    =  T_CARET
 * - "    =  T_DOUBLE_QUOTE
 * - '    =  T_SINGLE_QUOTE
 * - |    =  T_PIPE
 *
 *
 * We can also use some combo to make life easier.
 * A combo is sort of a predefined combination of tokens, this makes the creation of patterns faster.
 * A combo should start with C_.
 * For instance, one can imagine the following combo:
 *      C_ARRAY_REFERENCE, which would be replaced by this (for instance):
 *              T_LEFT_BRACKET  T_CONSTANT_ENCAPSED_STRING | T_LNUMBER | T_DNUMBER | T_VARIABLE  T_RIGHT_BRACKET
 *
 *
 */
interface SoundRegexEngineInterface
{

    /**
     * @param $pattern , string|array, the pattern to search for.
     *                      If it's a string, token names should be separated by at least one space.
     *                      token names should start with either T_ (php tokens) or with an X_ (user token from the preparser)
     *                      For instance:
     *                          T_VARIABLE  T_WHITESPACE? T_EQUALS  (T_CONSTANT_ENCAPSED_STRING | X_ARRAY_REFERENCE )  T_SEMI_COLON
     *
     *
     * @param $matches , array.
     *              It contains the matches.
     *              matches[0] will contain the matchingInfo that matched the full pattern;
     *              matches[1] will contain the matchingInfo that matched the first captured
     *                      parenthesized subpattern, and so on.
     *
     *
     *                  matchingInfo is an array with the following entries:
     *                          0: string, internal text that matched (for debug purpose)
     *                          1: int, the offset at which the match was found
     *                          2: string, the text that matched the pattern, or subpattern
     *                          3: array, the tokens that matched the pattern, or subpattern
     *
     *
     * @return bool, whether the match was successful or not.
     */
    public function match($pattern, $string, array &$matches = [], $offset = 0);

    /**
     * Performs a global sound regular expression match.
     *
     * @param $pattern , see match method
     * @param $string , see match method
     * @param array $matches , array of all matches as defined in the match method
     * @param int $offset , see match method
     * @return bool, whether at least one match was successful or not.
     */
    public function matchAll($pattern, $string, array &$matches = [], $offset = 0);


    /**
     * 
     * Performs a global sound regular expression and replace using a callback.
     * 
     * @param $pattern , see match method
     * @param $callback, the user callback,
     * 
     *              string   callback ( matchInfo )
     *                      it should return the replacement string.
     *                      The matchInfo argument is he same as the matchingInfo of the match method (see above).
     *                          
     *                      
     *          
     * @param $string , see match method
     * @return string, the replaced string
     * 
     * 
     *          Personal note:
     *                  replaceCallback usually doesn't work well with a pre-parser that use nested
     *                  elements; that's because the wrapping elements are generally replaced first,
     *                  and so the nested elements don't exist by the time the processor reaches them.
     * 
     *                  Therefore I believe that one shouldn't use pre-parser nestedMode with replaceCallback. 
     *  
     */
    public function replaceCallback($pattern, $callback, $string);

    public function setCombo($comboName, $pattern);

    public function getCombos();

    /**
     * @return false|string
     */
    public function getCombo($comboName);

    public function setPreParser(PreParserInterface $preparser);

    /**
     * @return PreParserInterface|null
     */
    public function getPreParser();
}
