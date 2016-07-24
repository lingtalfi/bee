<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Text\TypeWriter;


/**
 * TypeWriterInterface
 * @author Lingtalfi
 * 2015-03-23
 *
 * A type writer writes text in a canvas with fixed width and height
 *
 */
interface TypeWriterInterface
{


    public function printChars($chars);

    public function carriageReturn($n = 1);
}
