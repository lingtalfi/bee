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

use Bee\Component\Console\StreamWrapper\Writable\OutputStreamWrapper\OutputStreamWrapperInterface;
use Bee\Component\Text\TypeWriter\ConsoleTypeWriter;


/**
 * ConsoleExpressionWriter
 * @author Lingtalfi
 * 2015-03-23
 *
 */
class ConsoleExpressionWriter extends ExpressionWriter
{


    public function __construct(OutputStreamWrapperInterface $output)
    {
        parent::__construct();
        $this->setTypeWriter(new ConsoleTypeWriter($output));;
    }
}
