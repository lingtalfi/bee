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

use BeeSandBox\Language\Parser\Lexer\LookaheadLexer;


/**
 * LLkListParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
class LLkListParser extends LLkParser
{

    /** elementsList : '[' elements ']' ; // match bracketed list */
    public function elementsList()
    {
        $this->match(LookaheadLexer::LBRACK);
        $this->elements();
        $this->match(LookaheadLexer::RBRACK);

    }

    /** elements : element (',' element)* ; // match comma-separated list */
    public function elements()
    {
        $this->element();
        while (LookaheadLexer::COMMA === $this->LA(1)) {
            $this->match(LookaheadLexer::COMMA);
            $this->element();
        }

    }


    /** element : NAME '=' NAME | NAME | elementsList ; assignment, NAME or list */
    public function element()
    {
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
    }
}
