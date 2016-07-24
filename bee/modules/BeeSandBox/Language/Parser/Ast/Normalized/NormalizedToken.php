<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Normalized;


/**
 * NormalizedToken
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class NormalizedToken
{

    const INVALID_TOKEN_TYPE = 0;
    const PLUS = 1;
    const INT = 2;

    public $type;
    public $text;

    public function __construct($type, $text = null)
    {
        if (null !== $text) {
            $this->text = $text;
        }
        else {
            $this->text = '';
        }
        $this->type = $type;
    }


    public function __toString()
    {
        return $this->text;
    }


}
