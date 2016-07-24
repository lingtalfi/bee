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
 * ExprNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class ExprNode extends NormalizedAst
{

    const tINVALID = 0;
    const tINTEGER = 1;
    const tVECTOR = 2;

    protected $evalType;

    public function __construct($token = null)
    {
        parent::__construct($token); 
        $this->evalType = self::tINVALID;
    }


    public function getEvalType()
    {
        return $this->evalType;
    }

    public function __toString()
    {
        if (self::tINVALID !== $this->evalType) {
            $s = parent::__toString();
            $s .= '{type=';
            $s .= (self::tINTEGER === $this->evalType) ? "tINTEGER" : "tVECTOR";
            $s .= '}';
            return $s;
        }
        return parent::__toString();
    }
}
