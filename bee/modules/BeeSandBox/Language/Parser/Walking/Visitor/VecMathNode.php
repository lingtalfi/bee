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
 * VecMathNode
 * @author Lingtalfi
 * 2015-06-29
 * 
 */
abstract class VecMathNode extends HeteroAst{

    public abstract function visit(VecMathVisitorInterface $visitor);

}
