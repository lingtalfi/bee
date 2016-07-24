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
 * PrintNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class PrintNode extends StatNode
{

    /**
     * @var ExprNode
     */
    public $value;

    public function __construct($token = null, ExprNode $value = null)
    {
        parent::__construct($token);
        $this->value = $value;
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS VecMathNode
    //------------------------------------------------------------------------------/
    public function visit(VecMathVisitorInterface $v)
    {
        $v->visit($this);
    }


}
