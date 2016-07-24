<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\Adr;


/**
 * AdrInterface
 * @author Lingtalfi
 * 2014-10-21 --> 2015-01-13
 *
 *
 */
interface AdrInterface
{


    /**
     *
     * Returns the set of assets to use in an application when
     * all its assets dependencies are given by the $libIds and $customAssets args.
     *
     *
     * @param $libIds : array of library identifiers.
     *                      Each identifier represent a library which might depend on another library.
     *                      Each identifier is also bound to an arbitrary number of assets.
     * @param $customAssets : array of on the fly declared assets,
     *                              which might also depend on libraries.
     *                             Each entry is an array with two entries:
     *                                          0: array, assets
     *                                          1: array=null, library identifiers
     *
     * @return array:
     *          - js: array of unique ordered (library dependencies) js assets
     *          - css: array of unique css assets
     */
    public function resolve(array $libIds, array $customAssets = []);
}
