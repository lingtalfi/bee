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
use BeeSandBox\Language\Parser\Lexer\LexerInterface;
use BeeSandBox\Language\Parser\Parser\Exception\NoViableAltException;
use BeeSandBox\Language\Parser\Parser\Exception\RecognitionException;


/**
 * MemoizingBacktrackingListParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
class MemoizingBacktrackingListParser extends MemoizingBacktrackingParser
{

    private $listMemo;


    public function __construct(LexerInterface $input)
    {
        parent::__construct($input);
        $this->listMemo = [];
    }




    //------------------------------------------------------------------------------/
    // DEFINES MemoizingBacktrackingParser
    //------------------------------------------------------------------------------/
    public function clearMemo()
    {
        $this->listMemo = [];
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /** stat : list EOF | assign EOF ; */
    public function stat()
    {
        if ($this->speculate_stat_alt1()) {
            $this->elementsList();
            $this->match(BacktrackLexer::EOF_TYPE);
        }
        elseif ($this->speculate_stat_alt2()) {
            a("predict alternative 2");
            $this->assign();
            $this->match(BacktrackLexer::EOF_TYPE);
        }
        else {
            throw new NoViableAltException("expecting stat, found " . $this->input->getTokenName($this->LT(1)->type));
        }
    }


    public function speculate_stat_alt1()
    {
        a("attempt alternative 1");
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
        a("attempt alternative 2");
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
        $failed = false;
        $startTokenIndex = $this->index(); // get current token position
        if (true === $this->isSpeculating() && true === $this->alreadyParsedRule($this->listMemo)) {
            return;
        }
        // must not have previously parsed list at tokenIndex; parse it
        try {
            $this->_elementsList();
        } catch (RecognitionException $e) {
            $failed = true;
            throw $e;
        } finally {
            if (true === $this->isSpeculating()) {
                $this->memoize($this->listMemo, $startTokenIndex, $failed);
            }
        }
    }



    public function _elementsList()
    {
        a("parse list rule at token index " . $this->index());
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
