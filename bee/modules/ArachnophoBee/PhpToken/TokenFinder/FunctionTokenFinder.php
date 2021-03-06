<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\TokenFinder;

use ArachnophoBee\PhpToken\TokenArrayIterator\TokenArrayIterator;
use ArachnophoBee\PhpToken\TokenArrayIterator\Tool\TokenArrayIteratorTool;
use ArachnophoBee\PhpToken\Tool\TokenTool;

/**
 * FunctionTokenFinder
 * @author Lingtalfi
 * 2015-04-08
 *
 * If finds a function, like for instance:
 *
 *          function(){
 *              echo "doo";
 *          }
 *
 */
class FunctionTokenFinder extends RecursiveTokenFinder
{


    /**
     * @return array of match
     *                  every match is an array with the following entries:
     *                          0: int startIndex
     *                                      the index at which the pattern starts
     *                          1: int endIndex
     *                                      the index at which the pattern ends
     *
     */
    public function find(array $tokens)
    {
        $ret = [];
        $tai = new TokenArrayIterator($tokens);
        $start = null;
        while ($tai->valid()) {
            $cur = $tai->current();
            if (null === $start) {
                if (TokenTool::match(T_FUNCTION, $cur)) {
                    $start = $tai->key();
                }
            }
            else {

                $found = false;
                TokenArrayIteratorTool::skipWhiteSpaces($tai);
                if (TokenTool::match('(', $tai->current())) {
                    if (true === TokenArrayIteratorTool::moveToCorrespondingEnd($tai)) {
                        $tai->next();
                        TokenArrayIteratorTool::skipWhiteSpaces($tai);
                        if (TokenTool::match('{', $tai->current())) {
                            if (true === TokenArrayIteratorTool::moveToCorrespondingEnd($tai)) {
                                $found = true;
                                $ret[] = [$start, $tai->key()];
                                $this->onMatchFound($start, $tai);
                                $start = null;
                            }
                        }
                    }
                }
                if (false === $found) {
                    $start = null;
                }
            }
            $tai->next();
        }

        return $ret;
    }
}
