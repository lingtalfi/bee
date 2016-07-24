<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\String\StringParser\ExpressionDiscovererModel;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\ExpressionDiscovererInterface;


/**
 * ExpressionDiscovererModelInterface
 * @author Lingtalfi
 * 2015-05-12
 * 
 */
interface ExpressionDiscovererModelInterface {


    /**
     * @return ExpressionDiscovererInterface
     */
    public function getExpressionDiscoverer();
}
