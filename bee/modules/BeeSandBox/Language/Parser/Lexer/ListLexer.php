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


/**
 * ListLexer
 * @author Lingtalfi
 * 2015-06-27
 *
 */
class ListLexer extends Lexer
{

    const NAME = 2;
    const COMMA = 3;
    const LBRACK = 4;
    const RBRACK = 5;

    public static $tokenNames = [
        'n\a',
        '<EOF>',
        'NAME',
        'COMMA',
        'LBRACK',
        'RBRACK',
    ];


    //------------------------------------------------------------------------------/
    // DEFINES Lexer
    //------------------------------------------------------------------------------/
    /**
     * @return Token
     * @throws \Exception if a token couldn't be created (invalid char for instance)
     */
    public function nextToken()
    {
        while (self::EOF !== $this->c) {
            if ('#' === $this->c) {
                $this->COMMENT();
                continue;
            }
            else {
                switch ($this->c) {
                    case ' ':
                    case "\t":
                    case "\n":
                    case "\r":
                        $this->WS();
                        continue;
                        break;
                    case ",":
                        $this->consume();
                        return new Token(self::COMMA, ",");
                        break;
                    case "[":
                        $this->consume();
                        return new Token(self::LBRACK, "[");
                        break;
                    case "]":
                        $this->consume();
                        return new Token(self::RBRACK, "]");
                        break;
                    default:
                        if (true === $this->isLetter()) {
                            return $this->NAME();
                        }
                        $this->error("Invalid character: " . $this->c);
                        break;
                }
            }
        }
        return new Token(self::EOF_TYPE, '<EOF>');
    }

    public function getTokenName($tokenType)
    {
        return self::$tokenNames[$tokenType];
    }

    //------------------------------------------------------------------------------/
    // RULES
    //------------------------------------------------------------------------------/
    private function WS()
    {
        while (
            ' ' === $this->c ||
            "\t" === $this->c ||
            "\n" === $this->c ||
            "\r" === $this->c
        ) {
            $this->consume();
        }
    }

    private function NAME()
    {
        $s = '';
        do {
            $s .= $this->c;
            $this->consume();
        } while (true === $this->isLetter());
        return new Token(self::NAME, $s);
    }

    private function COMMENT()
    {
        while (self::EOF !== $this->c) {
            $this->consume();
        }
    }

    //------------------------------------------------------------------------------/
    // UTILS
    //------------------------------------------------------------------------------/
    private function isLetter()
    {
        if (preg_match('!^\pL$!u', $this->c)) {
            return true;
        }
        return false;
    }
}
