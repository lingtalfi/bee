<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Walking\Visitor;


/**
 * HeteroAst
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class HeteroAst
{

    /**
     * @var Token
     */
    public $token;

    public function __construct($token = null)
    {
        if ($token instanceof Token) {
            $this->token = $token;
        }
    }

    public function toString()
    {
        if (null !== $this->token) {
            return $this->token->toString();
        }
        return 'no token!';
    }

}
