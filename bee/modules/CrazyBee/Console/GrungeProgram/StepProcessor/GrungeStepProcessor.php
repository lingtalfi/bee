<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\StepProcessor;

use CrazyBee\Console\GrungeProgram\NotationResolver\GrungeNotationResolver;
use CrazyBee\Console\StepDrivenProgram\StepProcessor\StepProcessor;


/**
 * GrungeStepProcessor
 * @author Lingtalfi
 * 2015-05-19
 *
 */
class GrungeStepProcessor extends StepProcessor
{

    /**
     * @var GrungeNotationResolver
     */
    private $resolver;


    public function setResolver(GrungeNotationResolver $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }


    protected function resolve($value)
    {
        return $this->resolver->parseValue($value);
    }


}
