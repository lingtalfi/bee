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
 * BananaUriMatcher
 * @author Lingtalfi
 * 2015-03-10
 *
 * The notation for the pattern used by this class is:
 *
 *      banana uri matching notation
 *
 */
class BananaUriMatcher extends AppleUriMatcher
{
    public function __construct()
    {
        parent::__construct();
        $this->useWildCard = true;
    }


}
