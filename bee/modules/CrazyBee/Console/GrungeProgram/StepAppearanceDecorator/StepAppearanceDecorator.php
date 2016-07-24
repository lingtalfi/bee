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
 * StepAppearanceDecorator
 * @author Lingtalfi
 * 2015-05-20
 *
 *
 * This decorator works by adding a prefix and suffix to various properties of the step.
 * It also has a general step prefix and suffix.
 *
 * An affixes array contains two properties:
 *      0: the prefix (string displayed before the element)
 *      1: the suffix (string displayed after the element)
 *
 *
 *
 * Head and tail affixes only apply if the head/tail is not the empty string
 *
 *
 */
class StepAppearanceDecorator implements StepAppearanceDecoratorInterface
{

    private $stepAffixes;
    private $headAffixes;
    private $tailAffixes;
    private $inputAffixes;
    private $questionAffixes;
    private $booleanAffixes;


    //------------------------------------------------------------------------------/
    // IMPLEMENTS StepAppearanceDecoratorInterface
    //------------------------------------------------------------------------------/
    public function decorate(StepInterface $step)
    {
        if (is_array($this->headAffixes)) {
            $head = $step->getHead();
            if ('' !== $head) {
                list($a, $b) = $this->affixesToString($this->headAffixes);
                $step->setHead($a . $head . $b);
            }
        }
        if (is_array($this->tailAffixes)) {
            $tail = $step->getTail();
            if ('' !== $tail) {
                list($a, $b) = $this->affixesToString($this->tailAffixes);
                $step->setTail($a . $tail . $b);
            }
        }


        if (is_array($this->stepAffixes)) {
            list($a, $b) = $this->affixesToString($this->stepAffixes);
            $step->setHead($a . $step->getHead());
            $step->setTail($step->getTail() . $b);
        }
    }

    public function decorateProperty($value, $property)
    {
        switch ($property) {
            case 'input':
                $this->doDecorateByProperty($value, $this->inputAffixes);
                break;
            case 'boolean':
                $this->doDecorateByProperty($value, $this->booleanAffixes);
                break;
            case 'question':
                $this->doDecorateByProperty($value, $this->questionAffixes);
                break;
            default:
                break;
        }
        return $value;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setBooleanAffixes(array $booleanAffixes)
    {
        $this->booleanAffixes = $booleanAffixes;
        return $this;
    }

    public function setHeadAffixes(array $headAffixes)
    {
        $this->headAffixes = $headAffixes;
        return $this;
    }

    public function setInputAffixes(array $inputAffixes)
    {
        $this->inputAffixes = $inputAffixes;
        return $this;
    }

    public function setQuestionAffixes(array $questionAffixes)
    {
        $this->questionAffixes = $questionAffixes;
        return $this;
    }

    public function setStepAffixes(array $stepAffixes)
    {
        $this->stepAffixes = $stepAffixes;
        return $this;
    }

    public function setTailAffixes(array $tailAffixes)
    {
        $this->tailAffixes = $tailAffixes;
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function affixesToString(array $affixes)
    {
        list($a, $b) = $affixes;
        return [
            (string)$a,
            (string)$b,
        ];
    }

    private function doDecorateByProperty(&$value, array $affixes = null)
    {
        if (is_array($affixes)) {
            list($a, $b) = $this->affixesToString($affixes);
            $value = $a . $value . $b;
        }
    }

}
