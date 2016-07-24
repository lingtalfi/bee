<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\GrungeDriver;

use CrazyBee\Console\GrungeProgram\NotationResolver\GrungeNotationResolver;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\Step;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;
use Komin\Component\Console\Dialog\Dialog;
use Komin\Component\Console\Dialog\Tool\BooleanDialogTool;
use Komin\Component\Console\Dialog\Tool\DialogListTool;
use Komin\Component\Console\Dialog\Tool\DialogRepeaterTool;


/**
 * StepExpander
 * @author Lingtalfi
 * 2015-05-20
 *
 */
class StepExpander
{


    public function expand(array $step)
    {
        $this->expandLetters($step);
        $step = $this->processGoto($step);
        return $step;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function expandLetters(array &$step)
    {
        $this->expandToParent($step, [
            'h' => 'head',
            't' => 'tail',
            'g' => 'goto',
        ]);

        $this->expandToAction($step, [
            'i' => 'input',
            'q' => 'question',
            'r' => 'response',
            'b' => 'boolean',
            'y' => 'yes',
            'n' => 'no',
            'd' => 'default',
            's' => 'storeAs',
            'e' => 'execute',
        ]);
    }


    private function expandToParent(array &$parent, array $letters)
    {
        foreach ($letters as $short => $long) {
            if (array_key_exists($short, $parent)) {
                $parent[$long] = $parent[$short];
                unset($parent[$short]);
            }
        }
    }

    private function expandToAction(array &$parent, array $letters)
    {
        foreach ($letters as $short => $long) {
            if (array_key_exists($short, $parent)) {
                $parent['actions'][$long] = $parent[$short];
                unset($parent[$short]);
            }
        }
    }

    private function processGoto(array $step)
    {
        $this->expandGotoToParent($step, [
            'head',
            'tail',
        ]);

        if (array_key_exists('actions', $step)) {
            if (array_key_exists('input', $step['actions']) && is_string($step['actions']['input'])) {
                $step['actions']['input'] = $this->expandGotoToChild($step['actions']['input']);
            }
            if (array_key_exists('response', $step['actions']) && is_array($step['actions']['response'])) {
                foreach ($step['actions']['response'] as $k => $v) {
                    if (is_string($v)) {
                        $step['actions']['response'][$k] = $this->expandGotoToChild($v);
                    }
                }
            }
            if (array_key_exists('boolean', $step['actions']) && is_string($step['actions']['boolean'])) {
                $step['actions']['boolean'] = $this->expandGotoToChild($step['actions']['boolean']);
            }
        }
        return $step;
    }

    private function expandGotoToChild($gotoString)
    {
        $ret = $gotoString;
        if (false !== $info = $this->extractGotoSymbol($gotoString)) {
            list($text, $goto) = $info;
            $ret = [
                'text' => $text,
                'goto' => $goto,
            ];
        }
        else{
            $ret = [
                'text' => $gotoString,
            ];
        }
        return $ret;
    }

    private function expandGotoToParent(array &$parent, array $keys)
    {
        foreach ($keys as $k) {
            if (array_key_exists($k, $parent)) {
                $value = $parent[$k];
                if (is_string($value)) {
                    if (false !== $info = $this->extractGotoSymbol($value)) {
                        list($text, $goto) = $info;
                        $parent['goto'] = $goto;
                        $parent[$k] = $text;
                    }
                }
            }
        }
    }

    private function extractGotoSymbol($string)
    {
        if (preg_match('!(.+)\$\[\s*([a-zA-Z0-9.-_]+)\s*\]$!Us', trim($string), $match)) {
            return [
                trim($match[1]),
                $match[2],
            ];
        }
        return false;
    }

}
