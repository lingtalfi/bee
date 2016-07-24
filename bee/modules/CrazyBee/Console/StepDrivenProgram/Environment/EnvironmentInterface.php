<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\StepDrivenProgram\Environment;


/**
 * EnvironmentInterface
 * @author Lingtalfi
 * 2015-05-18
 *
 */
interface EnvironmentInterface
{
    public function setVariables(array $variables);

    public function setVariable($name, $value);

    public function getVariables();

    public function getVariable($name, $default = null);

    public function hasVariable($name);


}
