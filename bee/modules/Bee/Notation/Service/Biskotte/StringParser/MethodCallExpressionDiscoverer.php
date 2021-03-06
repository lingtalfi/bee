<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\Service\Biskotte\StringParser;

use Bee\Notation\String\StringParser\ExpressionDiscoverer\Container\OptionalKeyContainerExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\HybridExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\Miscellaneous\FunctionExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\SimpleQuoteExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscovererModel\ExpressionDiscovererModel;


/**
 * MethodCallExpressionDiscoverer
 * @author Lingtalfi
 * 2015-05-26
 *
 */
class MethodCallExpressionDiscoverer extends FunctionExpressionDiscoverer
{
    public function __construct()
    {
        parent::__construct();
        $this
            ->setPattern('!^[a-zA-Z0-9_]+\s*\(!')
            ->allowRecursion(false);
    }


    protected function doPrepare()
    {
        $arr = new OptionalKeyContainerExpressionDiscoverer();
        $resultOf = new ResultOfExpressionDiscoverer();
        $arrModel = new ExpressionDiscovererModel($arr);
        $resModel = new ExpressionDiscovererModel($resultOf);
        $discoverers = [
            $arrModel,
            $resModel,
            new SimpleQuoteExpressionDiscoverer(),
            new HybridExpressionDiscoverer(),
        ];


        $arr
            ->setBeginSep('[')
            ->setEndSep(']')
            ->setKeyValueSep('=>')
            ->setValueSep(',')
            ->setImplicitKeys(false)
            ->setImplicitValues(false)
            ->setImplicitEntries(false)
            ->setDiscoverers($discoverers)
            ->setKeyDiscoverers([
                new SimpleQuoteExpressionDiscoverer(),
                HybridExpressionDiscoverer::create(),
            ]);
        $this
            ->setEndSep(')')
            ->setValueSep(',')
            ->setImplicitValues(false)
            ->setDiscoverers($discoverers);
    }


}
