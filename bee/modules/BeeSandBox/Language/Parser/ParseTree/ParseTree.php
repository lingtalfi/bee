<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\ParseTree;

use BeeSandBox\Language\Parser\Exception\ParserException;
use BeeSandBox\Language\Parser\Lexer\Token;


/**
 * ParseTree
 * @author Lingtalfi
 * 2015-06-29
 *
 */
abstract class ParseTree
{
    public $children;

    public function addChild($value)
    {
        if (is_string($value)) {
            $r = new RuleNode($value);
            $this->addChild($r);
            return $r;
        }
        elseif ($value instanceof Token) {
            $r = new TokenNode($value);
            $this->addChild($r);
            return $r;
        }
        elseif ($value instanceof ParseTree) {
            if (null === $this->children) {
                $this->children = [];
            }
            $this->children[] = $value;
        }
        else {
            throw new ParserException(sprintf("expected string, Token or ParserTree, %s given", gettype($value)));
        }
    }


}
