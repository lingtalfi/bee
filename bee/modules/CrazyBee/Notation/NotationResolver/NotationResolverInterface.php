<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Notation\NotationResolver;


/**
 * NotationResolverInterface
 * @author Lingtalfi
 * 2015-05-16
 *
 */
interface NotationResolverInterface
{

    /**
     * @para mixed value,
     *                  can be a string, or an array to parse recursively.
     *                  Other types of values are not interpreted and are returned as is.
     * 
     * 
     * Interprets the given string using a notation, and returns the result.
     * It might execute some method if the notation say so.
     */
    public function parseValue($value);
}
