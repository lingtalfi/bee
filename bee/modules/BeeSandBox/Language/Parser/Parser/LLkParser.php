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
 * LLkParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
abstract class LLkParser implements ParserInterface
{

    /**
     * @var LexerInterface
     */
    protected $input;

    /**
     * @var Token[]
     */
    protected $lookahead;

    private $onMatch;

    // how many lookahead symbols
    private $k;
    // circular index of next token position to fill
    private $p;

    public function __construct(LexerInterface $input, $k = 1)
    {
        $this->input = $input;
        $this->k = $k;
        $this->p = 0;
        $this->lookahead = [];
        for ($i = 1; $i <= $k; $i++) {
            $this->consume();
        }
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
        if ($this->LA(1) === $x) {
            if (null !== $this->onMatch) {
                call_user_func($this->onMatch, $x, $this->input, $this->LT(1));
            }
            $this->consume();
        }
        else {
            $this->error("expecting " . $this->input->getTokenName($x) . "; found " . $this->input->getTokenName($this->LT(1)->type));
        }
    }

    /**
     * Consumes the input.
     * @return void
     */
    public function consume()
    {
        $this->lookahead[$this->p] = $this->input->nextToken();
        $this->p = ($this->p + 1) % $this->k;
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

    public function LA($i)
    {
        return $this->LT($i)->type;
    }


    /**
     * @return Token
     */
    public function LT($i)
    {
        return $this->lookahead[($this->p + $i - 1) % $this->k];
    }





    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function error($m)
    {
        throw new ParserException($m);
    }
}
