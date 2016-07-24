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
 * 2015-06-02
 *
 */
interface UriMatcherInterface
{


    /**
     * @param string $pattern ,  its syntax depends on the concrete class
     * @param string $uri , the request uri to match the pattern against
     * @return false|array, the patternVars
     */
    public function match($pattern, $uri);
}
