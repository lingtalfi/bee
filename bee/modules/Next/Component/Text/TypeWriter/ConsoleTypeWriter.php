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

use Bee\Component\Console\StreamWrapper\Writable\OutputStreamWrapper\OutputStreamWrapperInterface;


/**
 * ConsoleTypeWriter
 * @author Lingtalfi
 * 2015-03-23
 *
 *
 */
class ConsoleTypeWriter implements TypeWriterInterface
{

    /**
     * @var OutputStreamWrapperInterface
     */
    protected $output;

    public function __construct(OutputStreamWrapperInterface $output = null)
    {
        $this->output = $output;
    }





    //------------------------------------------------------------------------------/
    // IMPLEMENTS TypeWriterInterface
    //------------------------------------------------------------------------------/
    public function printChars($chars)
    {
        $this->output->write($chars);
    }

    public function carriageReturn($n = 1)
    {
        $this->output->write(str_repeat("\n", $n));
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return OutputStreamWrapperInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function setOutput(OutputStreamWrapperInterface $output)
    {
        $this->output = $output;
    }

}
