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

use BeeSandBox\Language\Parser\Lexer\Token;


/**
 * TokenNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class TokenNode extends ParseTree
{

    /**
     * @var Token
     */
    public $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

}
