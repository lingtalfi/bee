<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Homogeneous;


/**
 * HomogeneousAst
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class HomogeneousAst
{

    /**
     * @var HomogeneousToken
     */
    private $token;

    /**
     * @var HomogeneousAst[]
     * // normalized list of children
     */
    private $children;

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
                $this->token = new HomogeneousToken($token);
            }
            elseif ($token instanceof HomogeneousToken) {
                $this->token = $token;
            }
            else {
                throw new \InvalidArgumentException(sprintf("expected null, int or HomogeneousToken, %s given", gettype($token)));
            }
        }
    }

    public function getNodeType()
    {
        return $this->token->type;
    }

    public function addChild(HomogeneousAst $t)
    {
        if (null === $this->children) {
            $this->children = [];
        }
        $this->children[] = $t;
    }

    public function isNil()
    {
        return (null === $this->token);
    }

    public function __toString()
    {
        return (null !== $this->token) ? $this->token->__toString() : 'nil';
    }

    public function toStringTree()
    {
        $n = count($this->children);
        if (null === $this->children || 0 === $n) {
            return $this->__toString();
        }
        $s = '';
        if (!$this->isNil()) {
            $s .= '(';
            $s .= $this->__toString();
            $s .= ' ';
        }
        for ($i = 0; $i < $n; $i++) {
            $t = $this->children[$i]; // normalized (unnamed) children
            if ($i > 0) {
                $s .= ' ';
            }
            $s .= $t->toStringTree();
        }
        if (!$this->isNil()) {
            $s .= ')';
        }
        return $s;
    }


}
