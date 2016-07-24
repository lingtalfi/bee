<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Parser;


/**
 * ParserInterface
 * @author Lingtalfi
 * 2015-06-28
 * 
 */
interface ParserInterface {

    /**
     * Compare expected tokens against the lookahead symbol (or buffer).
     * If lookahead token type matches x, consume & return else error.
     * 
     * @param int $x, the type of the token to consume
     * @return void
     */
    public function match($x);

    /**
     * Consumes the input.
     * @return void
     */
    public function consume();
}
