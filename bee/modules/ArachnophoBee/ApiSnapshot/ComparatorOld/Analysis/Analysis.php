<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\ComparatorOld\Analysis;

use Komin\Notation\String\MiniMl\Tool\MiniMlTool;


/**
 * Analysis
 * @author Lingtalfi
 * 2015-05-01
 *
 */
class Analysis
{

    protected $summary;
    protected $success;
    protected $analysisSteps;


    public function __construct()
    {
        $this->analysisSteps = [];
        $this->success = false;
        $this->summary = '';
    }


    public function getAnalysisSteps()
    {
        return $this->analysisSteps;
    }

    public function addAnalysisSteps($analysisStep)
    {
        $this->analysisSteps[] = $analysisStep;
    }

    public function setAnalysisSteps(array $analysisSteps)
    {
        $this->analysisSteps = $analysisSteps;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function printToScreen()
    {
        echo $this;
    }
    

    public function isSuccess()
    {
        return $this->success;
    }

    public function __toString()
    {

        $s = "";
        $s .= $this->title("Summary");
        $s .= $this->text($this->summary);
        $s .= $this->title("Steps");
        foreach ($this->analysisSteps as $step) {
            $s .= $this->text($step);
        }
        return $s;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function title($title)
    {
        $s = "";
        $s .= str_repeat("-", 15) . PHP_EOL;
        $s .= $title . PHP_EOL;
        $s .= str_repeat("-", 15) . PHP_EOL;
        return $this->output($s);
    }

    protected function text($text)
    {
        $s = "";
        $s .= $text . PHP_EOL;
        return $this->output($s);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function output($s)
    {
        $s = MiniMlTool::format($s);
        return $s;
    }

}
