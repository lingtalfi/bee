<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Chemical\Parameters;
use Bee\Component\Bag\ReadOnlyBagInterface;


/**
 * WithReadOnlyParameterBagInterface
 * @author Lingtalfi
 * 2015-06-09
 * 
 */
interface WithReadOnlyParameterBagInterface {

    /**
     * @return ReadOnlyBagInterface
     */
    public function params();
}
