<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Text\TypeWriter\ExpressionWriter;

use Bee\Component\Text\TypeWriter\TypeWriterInterface;


/**
 * ExpressionWriterInterface
 * @author Lingtalfi
 * 2015-03-23
 *
 */
interface ExpressionWriterInterface
{

    public function write($expression);

    public function setTypeWriter(TypeWriterInterface $typeWriter);

    /**
     * @return TypeWriterInterface
     */
    public function getTypeWriter();

    public function setCarriageReturnSymbol($symbol);

    public function getCarriageReturnSymbol();
}
