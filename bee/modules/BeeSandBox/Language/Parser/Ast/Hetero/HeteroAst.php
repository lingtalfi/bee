<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Hetero;


/**
 * HeteroAst
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class HeteroAst
{

    /**
     * @var HeteroToken
     */
    private $token;


    /**
     * @param Token|null|int
     *              - Token:
     *              - null:  for making nil-rooted nodes
     *              - int: Create node from token type; used mainly for imaginary tokens
     *
     *
     *
     */
    public function __construct($token = null)
    {
        if (null !== $token) {
            if (is_int($token)) {
                $this->token = new HeteroToken($token);
            }
            elseif ($token instanceof HeteroToken) {
                $this->token = $token;
            }
            else {
                throw new \InvalidArgumentException(sprintf("expected null, int or HomogeneousToken, %s given", gettype($token)));
            }
        }
    }

    public function __toString()
    {
        return (null !== $this->token) ? $this->token->__toString() : 'nil';
    }

    public function toStringTree()
    {
        return $this->__toString();
    }


}
