<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\File\IndentedLines\NodeToArrayConvertor;

use Bee\Notation\File\IndentedLines\Node\NodeInterface;
use Bee\Notation\File\IndentedLines\ValueInterpreter\ValueInterpreterInterface;


/**
 * NodeToArrayConvertorInterface
 * @author Lingtalfi
 * 2015-02-27
 *
 */
interface NodeToArrayConvertorInterface
{

    /**
     * @return array
     */
    public function convert(NodeInterface $node, ValueInterpreterInterface $interpreter);
}
