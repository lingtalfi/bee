<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\ComparatorOld;

use ArachnophoBee\ApiSnapshot\ComparatorOld\Analyser\Analyser;
use ArachnophoBee\ApiSnapshot\ComparatorOld\Analyser\LinearAnalyser;
use ArachnophoBee\ApiSnapshot\ComparatorOld\Exception\InterruptProcessFlowException;
use ArachnophoBee\ApiSnapshot\ApiSnapshot;
use ArachnophoBee\ApiSnapshot\ApiSnapshotInterface;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;


/**
 * ApiSnapshotComparatorOld
 * @author Lingtalfi
 * 2015-04-30
 *
 */
class ApiSnapshotComparatorOld implements ApiSnapshotComparatorOldInterface
{


    protected $reportMode;
    protected $format;
    protected $analyser;

    // inside loop vars
    private $_currentClass;
    private $_currentProperty;
    private $_currentMethod;


    /**
     * @param array $options :
     *              - format: string, html(default)|cli
     *              - reportMode: string,
     *                      - full: get a complete report
     *                      - break (default): stop when a critical compatibility issue has been found
     */
    public function __construct(array $options = [])
    {
        $this->format = (array_key_exists('format', $options)) ? $options['format'] : 'html';
        $this->reportMode = (array_key_exists('reportMode', $options)) ? $options['reportMode'] : 'break';
        $this->analyser = new LinearAnalyser();
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ApiSnapshotComparatorOldInterface
    //------------------------------------------------------------------------------/
    /**
     * @return bool, whether at least one critical compatibility issue has been detected between
     *                      the two module versions.
     */
    public function compareByDumpFiles($moduleNamespace, $dumpFile1, $dumpFile2)
    {
        if (file_exists($dumpFile1)) {
            if (file_exists($dumpFile2)) {


                $v1 = new ApiSnapshot(BabyYamlTool::parseFile($dumpFile1));
                $v2 = new ApiSnapshot(BabyYamlTool::parseFile($dumpFile2));

                try {

                    if ('break' === $this->reportMode) {
                        $this->fastCompare($v1, $v2);
                    }
                    else {
                        $this->compareSnapshots($v1, $v2);


                    }


                } catch (InterruptProcessFlowException $e) {
                    $this->msg("critical compatibility issue detected, aborting process!", "debug");
                }


            }
            else {
                throw new \InvalidArgumentException("dumpFile2 not found: $dumpFile2");
            }
        }
        else {
            throw new \InvalidArgumentException("dumpFile1 not found: $dumpFile1");
        }
        return $this->analyser->getAnalysis()->isSuccess();
    }


    public function getAnalysis()
    {
        return $this->analyser->getAnalysis();
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function compareSnapshots(ApiSnapshotInterface $v1, ApiSnapshotInterface $v2)
    {

        $classes1 = $v1->getArray()['classes'];
        $classes2 = $v2->getArray()['classes'];


        $this->diff("class", "className", $classes1, $classes2);
        $this->_currentClass = null;
        $this->_currentProperty = null;
        $this->_currentMethod = null;
        foreach ($classes1 as $className => $classInfo1) {
            if (array_key_exists($className, $classes2)) {

                $this->_currentClass = $className;

                $classInfo2 = $classes2[$className];

                $this->compare("class", "comments", $classInfo1['comments'], $classInfo2['comments']);
                $this->compare("class", "signature", $classInfo1['signature'], $classInfo2['signature']);
                $this->compare("class", "dependencies", $classInfo1['dependencies'], $classInfo2['dependencies']);


                // properties
                $props1 = $classInfo1['properties'];
                $props2 = $classInfo2['properties'];
                $this->diff("property", "propName", $props1, $props2);
                foreach ($props1 as $propName => $propInfo1) {
                    if (array_key_exists($propName, $props2)) {
                        $this->_currentProperty = $propName;
                        $propInfo2 = $props2[$propName];
                        $this->compare("property", "comments", $propInfo1['comments'], $propInfo2['comments']);
                        $this->compare("property", "signature", $propInfo1['signature'], $propInfo2['signature']);
                    }
                }


                // methods
                $methods1 = $classInfo1['methods'];
                $methods2 = $classInfo2['methods'];
                $this->diff("method", "methodName", $methods1, $methods2);
                foreach ($methods1 as $methodName => $methodInfo1) {
                    if (array_key_exists($methodName, $methods2)) {
                        $this->_currentMethod = $methodName;
                        $methodInfo2 = $methods2[$methodName];
                        $this->compare("method", "comments", $methodInfo1['comments'], $methodInfo2['comments']);
                        $identical = $this->compare("method", "signature", $methodInfo1['signature'], $methodInfo2['signature']);


                        // args
                        if (false === $identical) {
                            $args1 = $methodInfo1['args'];
                            $args2 = $methodInfo2['args'];
                            $this->diff("arg", "argName", $args1, $args2);
                        }
                    }
                }
            }
        }
        $this->_currentClass = null;
        $this->_currentProperty = null;
        $this->_currentMethod = null;

    }


    protected function fastCompare(ApiSnapshotInterface $v1, ApiSnapshotInterface $v2)
    {
        $this->msg("comparing snapshots...", "step");

        if ($v1->getArray() !== $v2->getArray()) {
            $this->msg("both versions aren't identical. Run the process with reportMode to full to get details", "critical");
        }
        else {
            $this->msg("...ok (both versions are identical)", "debug");
        }
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function diff($scope, $objectName, $v1, $v2)
    {
        switch ($scope) {
            case 'class':
            case 'property':
            case 'method':

                $things1 = array_keys($v1);
                $things2 = array_keys($v2);
                $removed = array_diff($things1, $things2);
                if ($removed) {
                    $this->removed($scope, $objectName, $removed);
                }

                $added = array_diff($things2, $things1);
                if ($added) {
                    $this->added($scope, $objectName, $added);
                }

                break;
            case 'arg':

                // first convert numeric keys to assoc
                $args1 = [];
                $args2 = [];
                foreach ($v1 as $inf) {
                    $args1[$inf['name']] = $inf;
                }
                foreach ($v2 as $inf) {
                    $args2[$inf['name']] = $inf;
                }
                $things1 = array_keys($args1);
                $things2 = array_keys($args2);
                $removed = array_diff($things1, $things2);
                if ($removed) {
                    $this->removed($scope, $objectName, $removed);
                }

                $added = array_diff($things2, $things1);
                if ($added) {
                    $this->added($scope, $objectName, $added);
                }


                break;
            default:
                break;
        }

        $this->msg("Diff in $scope with $objectName", "debug");
    }

    private function compare($scope, $objectName, $v1, $v2)
    {
        $ret = true;
        switch ($scope) {
            case 'class':
            case 'property':
            case 'method':
                if ($v1 !== $v2) {
                    $this->updated($scope, $objectName, $v1, $v2);
                    $ret = false;
                }


                break;
            default:
                break;
        }

        $this->msg("compare in $scope with $objectName", "debug");
        return $ret;
    }


    private function msg($msg, $type)
    {
//        switch ($type) {
//            case 'step':
//            case 'critical':
//            case 'debug':
//                break;
//            default:
//                break;
//        }
//        echo $msg;
//        echo "<br>";
    }

    private function removed($scope, $objectName, $removed)
    {
        $this->analyser->addObservation('remove', $scope, $objectName, $this->getCurrentContextInfo(), $removed);
        $removedString = implode(PHP_EOL, $removed);
        $this->msg("Some $scope:$objectName have been removed: $removedString", "debug");
    }

    private function added($scope, $objectName, $added)
    {
        $this->analyser->addObservation('add', $scope, $objectName, $this->getCurrentContextInfo(), $added);
        $addedString = implode(PHP_EOL, $added);
        $this->msg("Some $scope:$objectName have been added: $addedString", "debug");
    }

    private function updated($scope, $objectName, $v1, $v2)
    {
        $this->analyser->addObservation('update', $scope, $objectName, $this->getCurrentContextInfo(), $v1, $v2);
    }

    private function getCurrentContextInfo()
    {
        return [
            $this->_currentClass,
            $this->_currentMethod,
            $this->_currentProperty,
        ];
    }

}
