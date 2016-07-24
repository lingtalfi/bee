<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Normalized;


/**
 * AddNode
 * @author Lingtalfi
 * 2015-06-29
 */
class AddNode extends ExprNode
{

    /**
     * @return AddNode
     */
    public static function constructAddNode(ExprNode $left, NormalizedToken $token, ExprNode $right)
    {
        $o = new AddNode($token);
        $o->addChild($left);
        $o->addChild($right);
        return $o;
    }


    public function getEvalType()
    {
        $left = $this->children[0];
        $right = $this->children[1];
        /**
         * @var ExprNode $left
         */
        /**
         * @var ExprNode $right
         */
        if (self::tINTEGER === $left->getEvalType() && self::tINTEGER === $right->getEvalType()) {
            return self::tINTEGER;
        }
        if (self::tVECTOR === $left->getEvalType() && self::tVECTOR === $right->getEvalType()) {
            return self::tVECTOR;
        }
        return self::tINVALID;
    }


}
