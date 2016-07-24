<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast;

use BeeSandBox\Language\Parser\Lexer\Token;


/**
 * Ast
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class Ast
{

    /**
     * @var Token
     */
    private $token;

    /**
     * @var Ast[]
     */
    private $children;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function addChild(Ast $t)
    {
        if (null === $this->children) {
            $this->children = [];
        }
        $this->children[] = $t;
    }


}
