<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\ArachnidParser;

use ArachnophoBee\PhpToken\ArachnidParser\ArachnidParserStep\ArachnidParserStepInterface;


/**
 * BaseArachnidParser
 * @author Lingtalfi
 * 2015-04-12
 *
 *
 */
class BaseArachnidParser implements ArachnidParserInterface
{


    protected $steps;

    public function __construct()
    {
        $this->steps = [];
    }





    //------------------------------------------------------------------------------/
    // IMPLEMENTS ArachnidParserInterface
    //------------------------------------------------------------------------------/
    /**
     * Executes the steps on the given content,
     * and returns the result.
     *
     * @return string
     */
    public function parse($content)
    {
        foreach ($this->steps as $name => $step) {
            if ($step instanceof ArachnidParserStepInterface) {
                $content = $step->execute($content);
                $this->onStepContentAfter($name, $content);
            }
        }
        return $content;
    }

    /**
     * @param array $steps , array of id => $steps
     */
    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function removeStep($id)
    {
        if (array_key_exists($id, $this->steps)) {
            unset($this->steps[$id]);
        }
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function onStepContentAfter($stepName, $content)
    {

    }

}
