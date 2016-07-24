<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Lexer\Tool;

use BeeSandBox\Language\Parser\Lexer\Lexer;


/**
 * LexerDevTool
 * @author Lingtalfi
 * 2015-06-27
 *
 */
class LexerDevTool
{


    public static function dumpTokens(Lexer $lexer, array $tokenNames = [])
    {
        $lexer->rewind();
        $t = $lexer->nextToken();

        echo '
<style>
    table, tr, td{
        border: 1px solid black;
        border-collapse: collapse;
    }
    td{
        padding:2px;
    }

</style>
';
        echo '<table>';
        while (Lexer::EOF_TYPE !== $t->type) {
            $tName = $tokenNames[$t->type];
            echo '<tr>';
            echo '<td>' . $tName . '</td>';
            echo '<td>' . $t->text . '</td>';
            echo '</tr>';


            $t = $lexer->nextToken();
        }
        echo '</table>';
    }

}
