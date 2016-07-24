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
 * AddNode
 * @author Lingtalfi
 * 2015-06-29
 */
class AddNode extends ExprNode
{

    /**
     * @var ExprNode
     * named, node-specific, irregular children
     */
    private $left;
    /**
     * @var ExprNode
     * named, node-specific, irregular children
     */
    private $right;

    /**
     * @return AddNode
     */
    public static function constructAddNode(ExprNode $left, HeteroToken $token, ExprNode $right)
    {
        $o = new AddNode($token);
        $o->left = $left;
        $o->right = $right;
        return $o;
    }


    public function toStringTree()
    {
        if (null === $this->left || null === $this->right) {
            return $this->__toString();
        }
        $s = '';
        $s .= '(';
        $s .= $this->__toString();
        $s .= ' ';
        $s .= $this->left->toStringTree();
        $s .= ' ';
        $s .= $this->right->toStringTree();
        $s .= ')';
        return $s;
    }

}
