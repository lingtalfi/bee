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
use ArachnophoBee\PhpToken\TokenArrayIterator\TokenArrayIteratorInterface;
use ArachnophoBee\PhpToken\TokenArrayIterator\Tool\TokenArrayIteratorTool;

/**
 * RecursiveTokenFinder
 * @author Lingtalfi
 * 2015-04-08
 *
 *
 *
 * Nested elements can also be found with nestedMode enabled (disabled by default).
 *
 *          - new Poo(new Poo())
 *
 *
 * in case of an object instantiation search.
 *
 *
 *
 *
 */
abstract class RecursiveTokenFinder implements TokenFinderInterface
{


    protected $nestedMode;

    public function __construct()
    {
        $this->nestedMode = false;
    }

    public function isNestedMode()
    {
        return $this->nestedMode;
    }

    public function setNestedMode($nestedMode)
    {
        $this->nestedMode = $nestedMode;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function onMatchFound($start, TokenArrayIteratorInterface $tai)
    {
        if (true === $this->nestedMode) {
            $tai->seek($start);
            $tai->next();
        }
    }

}
