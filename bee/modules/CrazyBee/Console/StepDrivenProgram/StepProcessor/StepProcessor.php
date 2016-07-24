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

use CrazyBee\Console\StepDrivenProgram\Environment\Environment;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;


/**
 * StepProcessor
 * @author Lingtalfi
 * 2015-05-18
 *
 */
class StepProcessor implements StepProcessorInterface
{

    private $steps;
    private $environment;


    public function __construct()
    {
        $this->steps = [];
    }


    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS StepProcessorInterface
    //------------------------------------------------------------------------------/
    public function execute()
    {
        if (array_key_exists('main', $this->steps)) {
            $this->executeStep($this->steps['main']);
        }
        else {
            throw new \RuntimeException("main step not found");
        }
    }

    public function registerStep($name, StepInterface $step)
    {
        $this->steps[$name] = $step;
        return $this;
    }

    public function getEnvironment()
    {
        if (null === $this->environment) {
            $this->environment = new Environment();
        }
        return $this->environment;
    }

    public function setEnvironment(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function registerSteps(array $steps)
    {
        foreach ($steps as $name => $step) {
            $this->registerStep($name, $step);
        }
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function executeStep(StepInterface $step)
    {

        $head = $step->getHead();
        if ('' !== $head) {
            echo $this->resolve($head);
        }
        $this->runActions($step);


        $tail = $step->getTail();
        if ('' !== $tail) {
            echo $this->resolve($tail);
        }

        
        // goto?
        $this->redirect($step);
        return $this;
    }


    private function runActions(StepInterface $step)
    {
        $actions = $step->getActions();
        foreach ($actions as $action) {
            if (is_callable($action)) {
                call_user_func($action, $step, $this->getEnvironment());
            }
            else {
                throw new \InvalidArgumentException(sprintf("action variable must be of type callable, %s given", gettype($action)));
            }
        }
    }


    private function redirect(StepInterface $step)
    {
        if (null !== $goto = $step->getGoto()) {
            $goto = $this->resolve($goto);
            if (is_string($goto)) {
                if (array_key_exists($goto, $this->steps)) {
                    $this->executeStep($this->steps[$goto]);
                }
                else {
                    throw new \RuntimeException("Step not found: $goto");
                }
            }
            else {
                throw new \InvalidArgumentException(sprintf("goto variable must be of type string, %s given", gettype($goto)));
            }
        }
    }


    protected function resolve($value)
    {
        return $value;
    }

}
