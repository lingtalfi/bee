<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\StepAppearanceDecorator;

use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;


/**
 * StepAppearanceDecoratorInterface
 * @author Lingtalfi
 * 2015-05-20
 *
 */
interface StepAppearanceDecoratorInterface
{

    public function decorate(StepInterface $step);

    /**
     * @return mixed, the decorated value
     */
    public function decorateProperty($value, $property);
}
