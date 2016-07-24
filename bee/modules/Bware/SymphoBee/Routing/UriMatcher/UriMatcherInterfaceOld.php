<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\Routing\UriMatcher;



/**
 * UriMatcherInterface
 * @author Lingtalfi
 * 2015-02-18
 * 
 */
interface UriMatcherInterfaceOld {


    /**
     * @param $pattern, string, its syntax depends on the concrete class.
     * @return false|array, the patternVars
     */
    public function match($uri, $pattern);
}
