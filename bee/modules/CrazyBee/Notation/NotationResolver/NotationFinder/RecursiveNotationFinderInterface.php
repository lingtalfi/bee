<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Notation\NotationResolver\NotationFinder;

use CrazyBee\Notation\NotationResolver\NotationResolverInterface;


/**
 * RecursiveNotationFinderInterface
 * @author Lingtalfi
 * 2015-05-17
 *
 *
 * This object needs to use the notation resolver internally.
 *
 */
interface RecursiveNotationFinderInterface
{

    public function setNotationResolver(NotationResolverInterface $r);
}
