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
use BeeSandBox\Language\Parser\Lexer\Token;
use BeeSandBox\Language\Parser\Parser\Exception\ParserException;


/**
 * LL1Parser
 * @author Lingtalfi
 * 2015-06-28
 *
 * In this implementation, the look ahead is a single token.
 * We could also have stored all tokens, but this implementation assumes that we cannot buffer all
 * the input (we might be reading from a socket).
 *
 */
abstract class LL1Parser implements ParserInterface
{

    /**
     * @var LexerInterface
     */
    protected $input;

    /**
     * @var Token
     */
    protected $lookahead;

    private $onMatch;

    public function __construct(LexerInterface $input)
    {
        $this->input = $input;
        $this->consume();
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS ParserInterface
    //------------------------------------------------------------------------------/
    /**
     * Compare expected tokens against the lookahead symbol (or buffer).
     * If lookahead token type matches x, consume & return else error.
     *
     * @param int $x , the type of the token to consume
     * @return void
     */
    public function match($x)
    {
        if ($this->lookahead->type === $x) {
            if (null !== $this->onMatch) {
                call_user_func($this->onMatch, $x, $this->input, $this->lookahead);
            }
            $this->consume();
        }
        else {
            $this->error("expecting " . $this->input->getTokenName($x) . "; found " . $this->input->getTokenName($this->lookahead->type));
        }
    }

    /**
     * Consumes the input.
     * @return void
     */
    public function consume()
    {
        $this->lookahead = $this->input->nextToken();
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @param callable $onMatch
     *              void    callable ( x, input, lookahead )
     *                                  - x: int, the matched token type
     *                                  - input: LexerInterface, the lexer
     *                                  - lookahead: Token, the token
     * @return $this
     */
    public function setOnMatch(callable $onMatch)
    {
        $this->onMatch = $onMatch;
        return $this;
    }





    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function error($m)
    {
        throw new ParserException($m);
    }
}
