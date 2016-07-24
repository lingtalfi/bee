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
 * StepInterface
 * @author Lingtalfi
 * 2015-05-18
 *
 */
interface StepInterface
{

    public function setHead($head);

    public function getHead();

    /**
     * @param $action, a callable
     *                      callable ( Step, Environment )         
     * 
     */
    public function setAction($action, $index = null);

    public function setActions(array $actions);

    public function getActions();

    public function setTail($tail);

    public function getTail();

    public function setGoto($goto);

    public function getGoto();
}
