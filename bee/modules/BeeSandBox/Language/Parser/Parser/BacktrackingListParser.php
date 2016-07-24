<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Parser;

use BeeSandBox\Language\Parser\Lexer\BacktrackLexer;
use BeeSandBox\Language\Parser\Parser\Exception\NoViableAltException;
use BeeSandBox\Language\Parser\Parser\Exception\RecognitionException;


/**
 * BacktrackingListParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
class BacktrackingListParser extends BacktrackingParser
{
    public function stat()
    {
        if ($this->speculate_stat_alt1()) {
            $this->elementsList();
            $this->match(BacktrackLexer::EOF_TYPE);
        }
        elseif ($this->speculate_stat_alt2()) {
            $this->assign();
            $this->match(BacktrackLexer::EOF_TYPE);
        }
        else {
            throw new NoViableAltException("expecting stat, found " . $this->input->getTokenName($this->LT(1)->type));
        }
    }


    public function speculate_stat_alt1()
    {
        $success = true;
        $this->mark(); // mark this spot in input so we can rewind
        try {
            $this->elementsList();
            $this->match(BacktrackLexer::EOF_TYPE);
        } catch (RecognitionException $e) {
            $success = false;
        }
        $this->release(); // either way, rewind to where we were
        return $success;
    }


    public function speculate_stat_alt2()
    {
        $success = true;
        $this->mark(); // mark this spot in input so we can rewind
        try {
            $this->assign();
            $this->match(BacktrackLexer::EOF_TYPE);
        } catch (RecognitionException $e) {
            $success = false;
        }
        $this->release(); // either way, rewind to where we were
        return $success;
    }


    /** assign : list '=' list ; // parallel assignment */
    public function assign()
    {
        $this->elementsList();
        $this->match(BacktrackLexer::EQUALS);
        $this->elementsList();
    }


    /** list : '[' elements ']' ; // match bracketed list */
    public function elementsList()
    {
        $this->match(BacktrackLexer::LBRACK);
        $this->elements();
        $this->match(BacktrackLexer::RBRACK);
    }

    /** elements : element (',' element)* ; // match comma-separated list */
    public function elements()
    {
        $this->element();
        while (BacktrackLexer::COMMA === $this->LA(1)) {
            $this->match(BacktrackLexer::COMMA);
            $this->element();
        }
    }

    /** element : name '=' NAME | NAME | list ; // assignment, name or list */
    public function element()
    {
        if (BacktrackLexer::NAME === $this->LA(1) && BacktrackLexer::EQUALS === $this->LA(2)) {
            $this->match(BacktrackLexer::NAME);
            $this->match(BacktrackLexer::EQUALS);
            $this->match(BacktrackLexer::NAME);
        }
        elseif (BacktrackLexer::NAME === $this->LA(1)) {
            $this->match(BacktrackLexer::NAME);
        }
        elseif (BacktrackLexer::LBRACK === $this->LA(1)) {
            $this->elementsList();
        }
        else {
            throw new NoViableAltException("Expecting element, found " . $this->input->getTokenName($this->LT(1)->type));
        }
    }
}
