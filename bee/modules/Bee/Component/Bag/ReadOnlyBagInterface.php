<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Bag;


/**
 * ReadOnlyBagInterface
 * @author Lingtalfi
 * 2015-06-01
 *
 */
interface ReadOnlyBagInterface
{

    public function has($name);

    public function get($name, $default = null);

    public function all();
}
