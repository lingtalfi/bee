<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\Variable\InlineVariableUtil\Adaptor;

use Bee\Bat\ReflectionTool;


/**
 * ClosureInlineVariableUtilAdaptor
 * @author Lingtalfi
 * 2015-04-26
 *
 */
class ClosureInlineVariableUtilAdaptor extends InlineVariableUtilAdaptor
{

    protected function getStringVersion($var, $type)
    {
        if ($var instanceof \Closure) {
            $o = new \ReflectionFunction($var);
            $args = [];
            foreach ($o->getParameters() as $p) {
                $args[] = ReflectionTool::getParameterAsString($p);
            }
            return 'closure(' . implode(', ', $args) . ')';
        }
        return false;
    }
}
