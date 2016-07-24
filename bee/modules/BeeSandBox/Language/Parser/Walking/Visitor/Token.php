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
 * Token
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class Token
{

    const INVALID_TOKEN_TYPE = 0;
    const PLUS = 1;
    const MULT = 2;
    const DOT = 3;
    const INT = 4;
    const VEC = 5;
    const ID = 6;
    const ASSIGN = 7;
    const _PRINT = 8;
    const STAT_LIST = 9;

    public $type;
    public $text;

    public function __construct($type, $text = null)
    {
        if (null !== $text) {
            $this->text = $text;
        }
        $this->type = $type;
    }

    public function toString()
    {
        return $this->text;
    }


}
