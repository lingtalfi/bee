<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\Environment;

use Bee\Application\ParameterBag\ParameterBagInterface;
use CrazyBee\Console\StepDrivenProgram\Environment\Environment;


/**
 * GrungeEnvironment
 * @author Lingtalfi
 * 2015-05-20
 *
 *
 */
class GrungeEnvironment extends Environment
{

    /**
     * @var ParameterBagInterface
     */
    private $paramBag;


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setVariable($name, $value)
    {
        if (false === $this->isParameter($name)) {
            return parent::setVariable($name, $value);
        }
        $this->paramBag->set($this->getParameter($name), $value);
        return $this;
    }

    public function getVariables()
    {

        $vars = parent::getVariables();
        $params = $this->paramBag->all();

        // note: we don't dive into every levels, it would be unmanageable;
        // but we still process the root level to give a hint to the user of what's going on
        foreach ($params as $path => $v) {
            $vars['p:' . $path] = $v;
        }
        return $vars;
    }

    public function getVariable($name, $default = null)
    {
        if (false === $this->isParameter($name)) {
            return parent::getVariable($name, $default);
        }
        return $this->paramBag->get($this->getParameter($name), $default);
    }

    public function hasVariable($name)
    {
        if (false === $this->isParameter($name)) {
            return parent::hasVariable($name);
        }
        return $this->paramBag->has($this->getParameter($name));
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setParameterBag(ParameterBagInterface $b)
    {
        $this->paramBag = $b;
        return $this;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function isParameter($name)
    {
        return ('p:' === substr($name, 0, 2));
    }

    private function getParameter($name)
    {
        return substr($name, 2);
    }
}
