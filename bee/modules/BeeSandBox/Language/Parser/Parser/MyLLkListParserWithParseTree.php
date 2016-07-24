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

use BeeSandBox\Language\Parser\Lexer\LexerInterface;
use BeeSandBox\Language\Parser\Lexer\LookaheadLexer;
use BeeSandBox\Language\Parser\ParseTree\ParseTree;
use BeeSandBox\Language\Parser\ParseTree\RuleNode;


/**
 * MyLLkListParserWithParseTree
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class MyLLkListParserWithParseTree extends LLkListParser
{

    /**
     * @var ParseTree
     * root of the parse tree
     */
    private $root;
    /**
     * @var ParseTree
     * the current node we're adding children to
     */
    private $currentNode;


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function match($x)
    {
        $this->currentNode->addChild($this->LT(1)); // add current lookahead token node
        parent::match($x);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function getParseTree()
    {
        return $this->root;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

    /** elementsList : '[' elements ']' ; // match bracketed list */
    public function elementsList()
    {
        $r = $this->_getRuleNode('elementsList');
        $_save = $this->currentNode;
        $this->currentNode = $r;


        $this->match(LookaheadLexer::LBRACK);
        $this->elements();
        $this->match(LookaheadLexer::RBRACK);


        $this->currentNode = $_save;


    }

    /** elements : element (',' element)* ; // match comma-separated list */
    public function elements()
    {

        $r = $this->_getRuleNode('elements');
        $_save = $this->currentNode;
        $this->currentNode = $r;


        $this->element();
        while (LookaheadLexer::COMMA === $this->LA(1)) {
            $this->match(LookaheadLexer::COMMA);
            $this->element();
        }


        $this->currentNode = $_save;
    }


    /** element : NAME '=' NAME | NAME | elementsList ; assignment, NAME or list */
    public function element()
    {

        $r = $this->_getRuleNode('element');
        $_save = $this->currentNode;
        $this->currentNode = $r;


        if (LookaheadLexer::NAME === $this->LA(1) && LookaheadLexer::EQUALS === $this->LA(2)) {
            $this->match(LookaheadLexer::NAME);
            $this->match(LookaheadLexer::EQUALS);
            $this->match(LookaheadLexer::NAME);
        }
        elseif (LookaheadLexer::NAME === $this->LA(1)) {
            $this->match(LookaheadLexer::NAME);
        }
        elseif (LookaheadLexer::LBRACK === $this->LA(1)) {
            $this->elementsList();
        }
        else {
            $this->error("Expecting name or list, found " . $this->input->getTokenName($this->LT(1)->type));
        }


        $this->currentNode = $_save;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function _getRuleNode($ruleName)
    {
        $r = new RuleNode($ruleName);
        if (null === $this->root) {
            $this->root = $r;
        }
        else {
            $this->currentNode->addChild($r);
        }
        return $r;
    }
}
