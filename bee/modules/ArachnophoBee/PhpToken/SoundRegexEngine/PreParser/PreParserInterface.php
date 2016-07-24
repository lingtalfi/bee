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


/**
 * PreParserInterface
 * @author Lingtalfi
 * 2015-04-08
 * 
 * 
 * A preParser helps parsing php scripts.
 * 
 * A preParser's goal is to take an array of tokens, and replace certain sequences of tokens symbolic tokens,
 * making it easier for further parsing to extract the information from a php script.
 *
 */
interface PreParserInterface
{


    /**
     * @param array $tokens,
     *              a token array as returned by php when we call the token_get_all() function.
     * @return array preParsed tokens,
     *              an array containing the new tokens.
     *              A token is a regular php token, or a custom token.
     *              A regular token is either:
     *                          - a string
     *                          - an array containing:
     *                                  0: int, type of token
     *                                  1: string, token content
     *                                  2: int, number of the line containing the content
     * 
     *              A custom token is an array containing:
     *                                  0: string, type of token. A user token starts with 
     *                                                  X_ prefix.
     * 
     *                                  1: string, token content
     *                                  2: int, number of the line containing the content                              
     *                                  3: array, the php tokens contained in the custom token
     *                                                  (a custom token is made of an 
     *                                                      arbitrary number of php tokens).
     *                                                                            
     *                  
     *                   
     */
    public function preparse(array $tokens);
}
