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


/**
 * ArachnidParserStepInterface
 * @author Lingtalfi
 * 2015-04-12
 *
 */
interface ArachnidParserStepInterface
{


    /**
     * Do something on the given content and returns the processed content.
     */
    public function execute($content);
}
