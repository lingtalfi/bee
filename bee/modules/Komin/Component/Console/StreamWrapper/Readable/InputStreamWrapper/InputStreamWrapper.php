<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komin\Component\Console\StreamWrapper\Readable\InputStreamWrapper;


/**
 * InputStreamWrapper
 * @author Lingtalfi
 * 2015-03-14
 *
 */
class InputStreamWrapper implements InputStreamWrapperInterface
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS InputStreamWrapperInterface
    //------------------------------------------------------------------------------/
    /**
     * @return object, a StreamWrapper object as defined in php
     *                  (generally STDIN).
     */
    public function getInput()
    {
        return STDIN;
    }
}
