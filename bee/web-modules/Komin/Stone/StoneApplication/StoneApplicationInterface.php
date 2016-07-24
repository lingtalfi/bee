<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Stone\StoneApplication;


/**
 * StoneApplicationInterface
 * @author Lingtalfi
 * 2014-11-02
 *
 */
interface StoneApplicationInterface
{

    public function start();

    /**
     * allowed keys depends on the implementation,
     * we recommend that a stone application allows at least for the following keys:
     *
     * - url
     * - lang (if multilang is used)
     *
     */
    public function getValue($key, $default = null);

    public function getParam($key, $default = null);
}
