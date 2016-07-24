<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\ArachnidParser\ArachnidParserStep;

use ArachnophoBee\PhpToken\ArachnidParser\Tools\DemystifierTool;


/**
 * ApplyAlphabetArachnidParserStep
 * @author Lingtalfi
 * 2015-04-12
 *
 */
class ApplyAlphabetArachnidParserStep extends BaseArachnidParserStep
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS ArachnidParserStepInterface
    //------------------------------------------------------------------------------/
    /**
     * Do something on the given content and returns the processed content.
     */
    public function execute($content)
    {
        $alphabets = DemystifierTool::getAlphabets($content);
        a($alphabets);
        return DemystifierTool::applyAlphabets($alphabets, $content);
    }

}
