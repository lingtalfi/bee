<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Lexer;

use BeeSandBox\Language\Parser\Lexer\Exception\LexerException;


/**
 * Lexer
 * @author Lingtalfi
 * 2015-06-27
 *
 */
abstract class Lexer implements LexerInterface
{
    const EOF = -1;
    const EOF_TYPE = 1;


    protected $p;
    protected $c;
    protected $input;

    // cache
    private $inputLen;

    public function __construct($input)
    {
        $this->input = $input;
        $this->inputLen = mb_strlen($input);
        $this->rewind();
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS LexerInterface
    //------------------------------------------------------------------------------/
    /**
     * @return Token
     * @throws \Exception if a token couldn't be created (invalid char for instance)
     */
    public abstract function nextToken();

    public abstract function getTokenName($tokenType);

    public function consume()
    {
        $this->p++;
        if ($this->p >= $this->inputLen) {
            $this->c = self::EOF;
        }
        else {
            $this->c = $this->charAt($this->p);
        }
    }

    public function match($x)
    {
        if ($this->c === $x) {
            $this->consume();
        }
        else {
            $this->error("expecting $x; found " . $this->c);
        }
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function rewind()
    {
        $this->p = 0;
        $this->c = $this->charAt($this->p);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function charAt($p)
    {
        return mb_substr($this->input, $p, 1);
    }

    protected function error($m)
    {
        throw new LexerException($m);
    }
}
