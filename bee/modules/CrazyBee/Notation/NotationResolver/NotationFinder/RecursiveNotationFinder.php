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
 * RecursiveNotationFinder
 * @author Lingtalfi
 * 2015-05-17
 *
 *
 */
class RecursiveNotationFinder extends NotationFinder implements RecursiveNotationFinderInterface
{

    /**
     * @var NotationResolverInterface
     */
    protected $notationResolver;

    public function __construct()
    {
        parent::__construct();
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS RecursiveNotationFinderInterface
    //------------------------------------------------------------------------------/
    public function setNotationResolver(NotationResolverInterface $r)
    {
        $this->notationResolver = $r;
    }
}
