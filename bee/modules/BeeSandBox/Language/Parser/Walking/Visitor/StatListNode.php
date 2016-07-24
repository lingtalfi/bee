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
 * StatListNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class StatListNode extends VecMathNode
{

    /**
     * @var StatNode[]
     */
    public $elements;

    public function __construct($token = null, array $elements = null)
    {
        parent::__construct(new Token(Token::STAT_LIST));// create imaginary token
        if (is_array($elements)) {
            $this->elements = $elements;
        }
        else {
            $this->elements = [];
        }
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS VecMathNode
    //------------------------------------------------------------------------------/
    public function visit(VecMathVisitorInterface $v)
    {
        $v->visit($this);
    }


}
