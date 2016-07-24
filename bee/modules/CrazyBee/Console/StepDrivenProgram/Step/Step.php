<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\StepDrivenProgram\Step;


/**
 * Step
 * @author Lingtalfi
 * 2015-05-18
 *
 */
class Step implements StepInterface
{

    private $head;
    private $actions;
    private $tail;
    private $goto;

    public function __construct()
    {
        $this->head = '';
        $this->actions = [];
        $this->tail = '';
        $this->goto = null;
    }


    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS GrungeStepInterface
    //------------------------------------------------------------------------------/
    public function setHead($head)
    {
        $this->head = $head;
        return $this;
    }

    public function getHead()
    {
        return $this->head;
    }

    public function setActions(array $actions)
    {
        $this->actions = $actions;
        return $this;
    }


    public function setAction($action, $index = null)
    {
        if (!is_callable($action)) {
            throw new \InvalidArgumentException(sprintf("action argument must be of type callable, %s given", gettype($action)));
        }
        if (null === $index) {
            $this->actions[] = $action;
        }
        else {
            $this->actions[$index] = $action;
        }
        return $this;
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function setTail($tail)
    {
        $this->tail = $tail;
        return $this;
    }

    public function getTail()
    {
        return $this->tail;
    }

    public function setGoto($goto)
    {
        $this->goto = $goto;
        return $this;
    }

    public function getGoto()
    {
        return $this->goto;
    }

}
