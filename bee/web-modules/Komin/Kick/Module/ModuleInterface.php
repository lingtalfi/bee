<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Kick\Module;


/**
 * ModuleInterface
 * @author Lingtalfi
 * 2015-01-12
 *
 */
interface ModuleInterface
{

    public function render();
    public function setParams(array $params);
    public function getParams();
}
