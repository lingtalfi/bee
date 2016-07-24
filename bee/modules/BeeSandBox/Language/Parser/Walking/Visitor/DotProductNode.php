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
 * DotProductNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class DotProductNode extends ExprNode
{
    /**
     * @var ExprNode
     */
    public $left;
    /**
     * @var ExprNode
     */
    public $right;


    public static function constructDotProductNode(ExprNode $left, Token $t, ExprNode $right)
    {
        $o = new DotProductNode($t);
        $o->left = $left;
        $o->right = $right;
        return $o;
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS VecMathNode
    //------------------------------------------------------------------------------/
    public function visit(VecMathVisitorInterface $v)
    {
        $v->visit($this);
    }


}
