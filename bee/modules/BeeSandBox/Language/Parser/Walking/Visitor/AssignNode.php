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
 * AssignNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class AssignNode extends StatNode
{

    /**
     * @var VarNode
     */
    public $id;

    /**
     * @var ExprNode
     */
    public $value;

    public static function constructAssignNode(VarNode $id, Token $t, ExprNode $value)
    {
        $o = new AssignNode($t);
        $o->id = $id;
        $o->value = $value;
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
