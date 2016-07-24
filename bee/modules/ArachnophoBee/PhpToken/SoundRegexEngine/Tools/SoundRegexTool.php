<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\SoundRegexEngine\Tools;


/**
 * SoundRegexTool
 * @author Lingtalfi
 * 2015-04-07
 *
 */
class SoundRegexTool
{


    public static function getSoundRegex(array $tokens)
    {
        return implode('', self::getSoundRegexTokens($tokens));
    }

    public static function getSoundRegexTokens(array $tokens)
    {
        $letter = 'K';
        array_walk($tokens, function (&$t) use ($letter) {
            if (is_string($t)) {
                switch ($t) {
                    case '(':
                        $t = $letter . '_LEFT_PARENTHESIS';
                        break;
                    case ')':
                        $t = $letter . '_RIGHT_PARENTHESIS';
                        break;
                    case '[':
                        $t = $letter . '_LEFT_BRACKET';
                        break;
                    case ']':
                        $t = $letter . '_RIGHT_BRACKET';
                        break;
                    case '{':
                        $t = $letter . '_LEFT_CURLY_BRACKET';
                        break;
                    case '}':
                        $t = $letter . '_RIGHT_CURLY_BRACKET';
                        break;
                    case '.':
                        $t = $letter . '_DOT';
                        break;
                    case ';':
                        $t = $letter . '_SEMI_COLON';
                        break;
                    case ',':
                        $t = $letter . '_COMMA';
                        break;
                    case '%':
                        $t = $letter . '_PERCENT';
                        break;
                    case '/':
                        $t = $letter . '_FORWARD_SLASH';
                        break;
                    case '-':
                        $t = $letter . '_DASH';
                        break;
                    case '+':
                        $t = $letter . '_PLUS';
                        break;
                    case '*':
                        $t = $letter . '_STAR';
                        break;
                    case '=':
                        $t = $letter . '_EQUALS';
                        break;
                    case '$':
                        $t = $letter . '_DOLLAR';
                        break;
                    case '@':
                        $t = $letter . '_AROBASE';
                        break;
                    case '<':
                        $t = $letter . '_LOWER_THAN';
                        break;
                    case '>':
                        $t = $letter . '_GREATER_THAN';
                        break;
                    case '&':
                        $t = $letter . '_AMPERSAND';
                        break;
                    case '!':
                        $t = $letter . '_EXCLAMATION_MARK';
                        break;
                    case '?':
                        $t = $letter . '_QUESTION_MARK';
                        break;
                    case ':':
                        $t = $letter . '_COLON';
                        break;
                    case '^':
                        $t = $letter . '_CARET';
                        break;
                    case '"':
                        $t = $letter . '_DOUBLE_QUOTE';
                        break;
                    case "'":
                        $t = $letter . '_SINGLE_QUOTE';
                        break;
                    case '|':
                        $t = $letter . '_PIPE';
                        break;
                    default:
                        throw new \RuntimeException(sprintf("Not implemented yet with token %s", $t));
                        break;
                }
            }
            elseif (is_array($t)) {
                if (is_int($t[0])) {
                    $t = $letter . substr(token_name($t[0]), 1);
                }
                else {
                    $t = $t[0];
                }
            }
            else {
                throw new \UnexpectedValueException("A token must be either a string or an array");
            }
        });
        return $tokens;
    }
}
