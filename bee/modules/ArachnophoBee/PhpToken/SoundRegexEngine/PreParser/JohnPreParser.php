<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\SoundRegexEngine\PreParser;

use ArachnophoBee\PhpToken\TokenFinder\ArrayReferenceTokenFinder;
use ArachnophoBee\PhpToken\Tool\TokenTool;


/**
 * JohnPreParser
 * @author Lingtalfi
 * 2015-04-08
 *
 */
class JohnPreParser extends PreParser
{
    public function __construct()
    {
        parent::__construct();
        $this->setFunctions([
            'X_ARRAY_REFERENCE' => [$this, 'parseArrayReference'],
        ]);
    }






    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function parseArrayReference(array $tokens)
    {
        $o = new ArrayReferenceTokenFinder();
//        $o->setNestedMode(true);
        $map = $o->find($tokens);
        return $this->applyMap($map, "X_ARRAY_REFERENCE", $tokens);
    }


}
