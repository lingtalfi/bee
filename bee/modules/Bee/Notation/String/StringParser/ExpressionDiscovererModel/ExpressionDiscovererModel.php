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
 * ExpressionDiscovererModel
 * @author Lingtalfi
 * 2015-05-12
 *
 */
class ExpressionDiscovererModel implements ExpressionDiscovererModelInterface
{

    private $discoverer;

    public function __construct(ExpressionDiscovererInterface $discoverer)
    {
        $this->discoverer = $discoverer;
    }
    

    //------------------------------------------------------------------------------/
    // IMPLEMENTS ExpressionDiscovererModelInterface
    //------------------------------------------------------------------------------/
    /**
     * @return ExpressionDiscovererInterface
     */
    public function getExpressionDiscoverer()
    {
        return clone $this->discoverer;
    }


}
