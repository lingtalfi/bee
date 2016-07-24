<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\StepDrivenProgram\StepProcessor;

use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;


/**
 * StepProcessorInterface
 * @author Lingtalfi
 * 2015-05-18
 *
 */
interface StepProcessorInterface
{

    public function execute();

    public function registerStep($name, StepInterface $step);

    public function setEnvironment(EnvironmentInterface $environment);

    public function getEnvironment();

}
