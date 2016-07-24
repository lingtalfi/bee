<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MacBee\Component\Rotator;


/**
 * RotatorInterface
 * @author Lingtalfi
 * 2015-07-03
 *
 */
interface RotatorInterface
{

    /**
     * Check if the resource identified by the identifier should be rotated or not.
     * If so, it rotates the resource (this usually means copying the resource to a backup dir).
     * 
     * Details on how it is done depends on the Rotator instance. 
     *
     * @return void
     */
    public function rotate($identifier);
}
