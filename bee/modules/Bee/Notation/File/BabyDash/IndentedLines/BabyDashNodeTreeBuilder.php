<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\File\BabyDash\IndentedLines;

use Bee\Notation\File\IndentedLines\NodeTreeBuilder\NodeTreeBuilder;


/**
 * BabyDashNodeTreeBuilder
 * @author Lingtalfi
 * 2015-04-17
 *
 */
class BabyDashNodeTreeBuilder extends NodeTreeBuilder
{


    public function __construct(array $options = [])
    {
        $options['indentChar'] = '-';
        $options['nbIndentCharPerLevel'] = 4;
        $options['hasLeadingIndentChar'] = true;
        $options['keyMode'] = 2;
        $options['useMultiline'] = false;
        parent::__construct($options);
    }


}
