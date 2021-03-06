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
use ArachnophoBee\PhpToken\TokenFinder\Tool\TokenFinderTool;
use ArachnophoBee\PhpToken\Tool\TokenTool;

/**
 * ClassTokenFinder
 * @author Lingtalfi
 * 2015-04-25
 *
 *
 * It assumes that the php code is valid.
 * If finds a className, like for instance if the given code is
 *
 *          class Doo{
 *              // ...
 *          }
 * 
 * It will also match Traits.
 *
 *
 * and matches Doo.
 *
 */
class ClassNameTokenFinder extends RecursiveTokenFinder
{


    protected $namespace;

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
                if (TokenTool::match([T_CLASS, T_TRAIT], $cur)) {
                    $start = $tai->key();
                }
            }
            else {

                $found = false;
                TokenArrayIteratorTool::skipWhiteSpaces($tai);
                $start = $tai->key();
                if (TokenArrayIteratorTool::skipNsChain($tai)) {
                    $found = true;

                    // skipNsChain ends AFTER the chain, not AT the end of it.
                    $tai->prev();
                    $end = $tai->key();
                    $tai->next();

                    $ret[] = [$start, $end];
                    $this->onMatchFound($start, $tai);
                    $start = null;
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
