<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\ParseTree\Tool;

use BeeSandBox\Language\Parser\ParseTree\ParseTree;
use BeeSandBox\Language\Parser\ParseTree\RuleNode;
use BeeSandBox\Language\Parser\ParseTree\TokenNode;


/**
 * DebugParseTreeTool
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class DebugParseTreeTool
{

    public static function renderParseTree(ParseTree $t, $indentFactor = 2)
    {
        $s = self::decorate('[ROOT]') . PHP_EOL;
        self::doRenderParseTree($s, $t, 1, $indentFactor);
        return nl2br($s);
    }


    private static function doRenderParseTree(&$s, ParseTree $t, $level = 1, $indentFactor = 2)
    {
        self::doRenderChildren($s, $t->children, $level, $indentFactor);
    }


    private static function doRenderChildren(&$s, $c, $level, $indentFactor = 2)
    {
        $eol = PHP_EOL;
        $indent = str_repeat('-', $indentFactor * $level) . ' ';
        if (is_array($c)) {
            foreach ($c as $node) {
                if ($node instanceof RuleNode) {
                    $s .= $indent . self::decorate($node->name) . $eol;
                    self::doRenderChildren($s, $node->children, $level + 1, $indentFactor);
                }
                elseif ($node instanceof TokenNode) {
                    $s .= $indent . $node->token->text . $eol;
                }
                elseif ($node instanceof ParseTree) {
                    self::doRenderParseTree($s, $node, $level + 1, $indentFactor);
                }
            }
        }
    }

    private static function decorate($m)
    {
        return '<b>' . $m . '</b>';
    }
}
