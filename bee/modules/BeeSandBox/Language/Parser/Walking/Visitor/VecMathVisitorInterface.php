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
 * VecMathVisitorInterface
 * @author Lingtalfi
 * 2015-06-29
 *
 */
interface VecMathVisitorInterface
{

    public function visit(VecMathNode $n);
}
