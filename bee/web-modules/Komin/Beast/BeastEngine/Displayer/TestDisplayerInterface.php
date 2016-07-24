<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\BeastEngine\Displayer;
use WebModule\Komin\Beast\BeastEngine\BeastEngineInterface;


/**
 * TestDisplayerInterface
 * @author Lingtalfi
 * 2015-01-24
 *
 * Goal of this class is to display the test results.
 *
 */
interface TestDisplayerInterface
{

    public function display(BeastEngineInterface $beastEngine);
}
