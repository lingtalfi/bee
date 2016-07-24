<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\NotationResolver;

use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Notation\NotationResolver\NotationFinder\NotationFinder;


/**
 * SessionVarNotationFinder
 * @author Lingtalfi
 * 2015-05-20
 *
 * is a notation finder which value are found in the environment variables
 *
 */
class SessionVarNotationFinder extends NotationFinder
{

    /**
     * @var EnvironmentInterface
     */
    private $environment;
    

    public function getValue()
    {
        $value = parent::getValue();
        if (null !== $this->environment) {
            if ($this->environment->hasVariable($value)) {
                $value = $this->environment->getVariable($value);
            }
            else {
                // when this finder doesn't match, 
                // it's a really bad idea to try to revert to the 
                // initial state of the expression,
                // because of the recursion which would enter an infinite loop
                $this->addWarning("Could not resolve the value \"$value\" with the current environment");
            }
        }
        return $value;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setEnvironment(EnvironmentInterface $e)
    {
        $this->environment = $e;
        return $this;
    }


}
