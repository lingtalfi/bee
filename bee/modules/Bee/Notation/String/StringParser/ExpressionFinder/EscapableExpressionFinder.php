<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\String\StringParser\ExpressionFinder;

use Bee\Notation\String\StringParser\ExpressionDiscoverer\Container\ContainerExpressionDiscoverer;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\ExpressionDiscovererInterface;


/**
 * EscapableExpressionFinder
 * @author Lingtalfi
 * 2015-06-04
 *
 * This finder will discard the expression if it is escaped by the chosen
 * escaping char.
 *
 */
class EscapableExpressionFinder extends ExpressionFinder
{

    private $escapeChar;

    public function __construct()
    {
        parent::__construct();
        /**
         * The expression is valid only if it not preceded by
         * a non blank char.
         */
        $this->setValidator(function ($s, $spos) {
            $spos -= 1;
            if ($spos < 0) {
                return;
            }
            $beforeChar = substr($s, $spos, 1);
            if ($this->escapeChar === $beforeChar) {
                return false;
            }
        });
    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    public function setEscapeChar($escapeChar)
    {
        $this->escapeChar = $escapeChar;
        return $this;
    }


}
