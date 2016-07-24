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
use BeeSandBox\Language\Parser\Parser\Exception\MismatchedTokenException;
use BeeSandBox\Language\Parser\Parser\Exception\ParserException;


/**
 * BacktrackingParser
 * @author Lingtalfi
 * 2015-06-28
 *
 */
abstract class BacktrackingParser implements ParserInterface
{

    /**
     * @var LexerInterface
     */
    protected $input;

    /**
     * @var Token[]
     * dynamically-sized lookahead buffer
     */
    protected $lookahead;

    /**
     * @var int[]
     * stack of index markers into lookahead buffer
     */
    protected $markers;

    private $onMatch;

    // index of current lookahead token
    // LT(1) returns lookahead[p]
    private $p;

    public function __construct(LexerInterface $input)
    {
        $this->input = $input;
        $this->lookahead = [];
        $this->markers = [];
        $this->p = 0;
        $this->sync(1);
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
            throw new MismatchedTokenException(
                "expecting " .
                $this->input->getTokenName($x) . "; found " .
                $this->input->getTokenName($this->LT(1)->type));
        }
    }

    /**
     * Consumes the input.
     * @return void
     */
    public function consume()
    {
        $this->p++;
        // have we hit end of buffer when not backtracking?
        if (count($this->lookahead) === $this->p && false === $this->isSpeculating()) {
            // if so, it's an opportunity to start filling at index 0 again
            $this->p = 0;
            $this->lookahead = [];
        }

        // get another to replace consumed token
        $this->sync(1);
    }
    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /** Make sure we have i tokens from current position p */
    public function sync($i)
    {
        if (($this->p + $i - 1) > (count($this->lookahead) - 1)) {
            $n = ($this->p + $i - 1) - (count($this->lookahead) - 1);
            $this->fill($n);
        }
    }

    // add n tokens
    public function fill($n)
    {
        for ($i = 1; $i <= $n; $i++) {
            $this->lookahead[] = $this->input->nextToken();
        }
    }

    public function mark()
    {
        $this->markers[] = $this->p;
        return $this->p;
    }

    public function release()
    {
        $m = count($this->markers) - 1;
        unset($this->markers[$m]);
        $this->seek($m);
    }

    public function seek($index)
    {
        $this->p = $index;
    }

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
        $this->sync($i);
        return $this->lookahead[$this->p + $i - 1];
    }

    public function isSpeculating()
    {
        return (count($this->markers) > 0);
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function error($m)
    {
        throw new ParserException($m);
    }
}
