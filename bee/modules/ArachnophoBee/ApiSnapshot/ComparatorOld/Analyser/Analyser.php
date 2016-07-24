<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\ComparatorOld\Analyser;

use ArachnophoBee\ApiSnapshot\ComparatorOld\Analysis\Analysis;


/**
 * Analyser
 * @author Lingtalfi
 * 2015-05-01
 *
 */
abstract class Analyser
{


    protected $analysis;
    protected $observations;
    protected $isPrepared;

    public function __construct()
    {
        $this->analysis = new Analysis();
        $this->observations = [];
        $this->isPrepared = false;
    }


    abstract protected function prepareAnalysis();


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function getAnalysis()
    {
        if (false === $this->isPrepared) {
            $this->prepareAnalysis();
            $this->isPrepared = true;
        }
        return $this->analysis;
    }


    public function addObservation($type, $scope, $objectName, array $currentInfo, $arg1 = null, $arg2 = null)
    {
        $this->observations[] = [
            $type,
            $scope,
            $objectName,
            $currentInfo,
            $arg1,
            $arg2
        ];
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function criticalMsg($msg)
    {
        $msg = '<red>Critical:</red> ' . $msg;
        $this->step($msg);
    }

    protected function infoMsg($msg)
    {
        $msg = '<info>Info:</info> ' . $msg;
        $this->step($msg);
    }

    protected function step($msg)
    {
        $this->analysis->addAnalysisSteps($msg);
    }

    protected function toList(array $items)
    {

        $s = '';
        if ($items) {
            foreach ($items as $item) {
                $s .= PHP_EOL;
                $s .= "- ";
                $s .= $item;
            }
        }
        return $s;
    }

    protected function diffList($items1, $items2)
    {
        $s = '';
        $s .= PHP_EOL;
        $s .= "(v1 value)";
        $s .= str_repeat("-", 15) . PHP_EOL;
        if (is_array($items1)) {
            $s .= $this->toList($items1) . PHP_EOL;
        }
        else {
            $s .= $items1 . PHP_EOL;
        }
        //
        $s .= "(v2 value)";
        $s .= str_repeat("-", 15) . PHP_EOL;
        if (is_array($items2)) {
            $s .= $this->toList($items2) . PHP_EOL;
        }
        else {
            $s .= $items2 . PHP_EOL;
        }

        return $s;
    }
}
