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
 * ExpressionWriter
 * @author Lingtalfi
 * 2015-03-23
 *
 */
class ExpressionWriter implements ExpressionWriterInterface
{

    /**
     * @var TypeWriterInterface
     */
    protected $typeWriter;
    protected $crSymbol;

    public function __construct()
    {
        $this->crSymbol = '<CR>';
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ExpressionWriterInterface
    //------------------------------------------------------------------------------/
    public function write($expression)
    {
        if ($this->typeWriter instanceof TypeWriterInterface) {

            $lines = implode($this->crSymbol, $expression);
            if ($lines) {
                $c = false;
                foreach ($lines as $line) {
                    if (true === $c) {
                        $this->typeWriter->carriageReturn();
                    }
                    $this->typeWriter->printChars($line);
                    $c = true;
                }
            }
        }
        else {
            throw new \LogicException("you must define a valid typeWriter before using the write method");
        }
    }

    public function setTypeWriter(TypeWriterInterface $typeWriter)
    {
        $this->typeWriter = $typeWriter;
    }

    /**
     * @return TypeWriterInterface
     */
    public function getTypeWriter()
    {
        return $this->typeWriter;
    }

    public function setCarriageReturnSymbol($symbol)
    {
        $this->crSymbol = $symbol;
    }

    public function getCarriageReturnSymbol()
    {
        return $this->crSymbol;
    }


}
