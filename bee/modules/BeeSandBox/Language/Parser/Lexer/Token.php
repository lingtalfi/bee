<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Lexer;


/**
 * Token
 * @author Lingtalfi
 * 2015-06-27
 *
 */
class Token
{
    public $type;
    public $text;

    public function __construct($type, $text)
    {
        $this->text = $text;
        $this->type = $type;
    }
}
