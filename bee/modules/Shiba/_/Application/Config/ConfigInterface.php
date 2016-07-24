<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shiba\Application\Config;


/**
 * ConfigInterface
 * @author Lingtalfi
 * 2015-04-03
 *
 */
interface ConfigInterface
{


    public function getParameters();

    public function getParameter($key, $ret = null);

}
