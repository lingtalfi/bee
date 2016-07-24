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
 * VarNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class VarNode extends ExprNode 
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS VecMathNode
    //------------------------------------------------------------------------------/
    public function visit(VecMathVisitorInterface $visitor)
    {
        $visitor->visit($this);
    }


}
