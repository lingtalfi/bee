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
 * LexerInterface
 * @author Lingtalfi
 * 2015-06-28
 *
 */
interface LexerInterface
{

    /**
     * @return Token
     * @throws \Exception if a token couldn't be created (invalid char for instance)
     */
    public function nextToken();

    public function getTokenName($tokenType); // do we really need this?

    public function consume();

    public function match($x);
}
