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

use BeeSandBox\Language\Parser\Lexer\ListLexer;


/**
 * LL1ListParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
class LL1ListParser extends LL1Parser
{

    public function elementsList()
    {
        $this->match(ListLexer::LBRACK);
        $this->elements();
        $this->match(ListLexer::RBRACK);

    }

    public function elements()
    {
        $this->element();
        while (ListLexer::COMMA === $this->lookahead->type) {
            $this->match(ListLexer::COMMA);
            $this->element();
        }

    }

    public function element()
    {
        if (ListLexer::NAME === $this->lookahead->type) {
            $this->match(ListLexer::NAME);
        }
        elseif (ListLexer::LBRACK === $this->lookahead->type) {
            $this->elementsList();
        }
        else {
            $this->error("Expecting name or list, found " . $this->input->getTokenName($this->lookahead->type));
        }
    }
}
