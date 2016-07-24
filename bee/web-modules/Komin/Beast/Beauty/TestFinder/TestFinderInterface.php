<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\Beauty\TestFinder;


/**
 * TestFinderInterface
 * @author Lingtalfi
 * 2015-01-25
 *
 */
interface TestFinderInterface
{

    /**
     * @return array:
     *
     * - $groupName:
     * ----- *$url
     * ----- *$groupName
     */
    public function getTests();
}
