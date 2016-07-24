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

use Bee\Bat\BdotTool;


/**
 * Environment
 * @author Lingtalfi
 * 2015-05-18
 *
 */
class Environment implements EnvironmentInterface
{

    private $variables;


    public function __construct()
    {
        $this->variables = [];
    }

    public static function create()
    {
        return new static();
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS EnvironmentInterface
    //------------------------------------------------------------------------------/
    public function setVariables(array $variables)
    {
        foreach ($this->variables as $k => $v) {
            $this->setVariable($k, $v);
        }
    }

    public function setVariable($name, $value)
    {
        BdotTool::setDotValue($name, $value, $this->variables);
        return $this;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function getVariable($name, $default = null)
    {
        $found = false;
        $v = BdotTool::getDotValue($name, $this->variables, null, $found);
        if (true === $found) {
            return $v;
        }
        return $default;
    }

    public function hasVariable($name)
    {
        $found = false;
        BdotTool::getDotValue($name, $this->variables, null, $found);
        return $found;
    }
}
