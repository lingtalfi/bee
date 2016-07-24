<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\Comparator;

use ArachnophoBee\ApiSnapshot\ApiSnapshot;
use ArachnophoBee\ApiSnapshot\ApiSnapshotInterface;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;


/**
 * ApiSnapshotComparator
 * @author Lingtalfi
 * 2015-05-04
 *
 */
class ApiSnapshotComparator
{

    private $removed;
    private $added;
    private $updated;


    //------------------------------------------------------------------------------/
    // IMPLEMENTS ApiSnapshotComparatorInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array, <snapshotComparison>, see documentation
     */
    public function compareByDumpFiles($apiSnapshotFile1, $apiSnapshotFile2)
    {
        $this->reset();
        if (file_exists($apiSnapshotFile1)) {
            if (file_exists($apiSnapshotFile2)) {


                $v1 = new ApiSnapshot(BabyYamlTool::parseFile($apiSnapshotFile1));
                $v2 = new ApiSnapshot(BabyYamlTool::parseFile($apiSnapshotFile2));
                $this->compareSnapshots($v1, $v2);
                return [
                    'added' => $this->added,
                    'removed' => $this->removed,
                    'updated' => $this->updated,
                ];
            }
            else {
                throw new \InvalidArgumentException("apiSnapshot 2 not found: $apiSnapshotFile2");
            }
        }
        else {
            throw new \InvalidArgumentException("apiSnapshot 1 not found: $apiSnapshotFile1");
        }
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function compareSnapshots(ApiSnapshotInterface $v1, ApiSnapshotInterface $v2)
    {

        $classes1 = $v1->getArray()['classes'];
        $classes2 = $v2->getArray()['classes'];


        $this->diff("class", "className", $classes1, $classes2);
        foreach ($classes1 as $className => $classInfo1) {
            if (array_key_exists($className, $classes2)) {


                $classInfo2 = $classes2[$className];

                $this->compare("class", "comments", $classInfo1['comments'], $classInfo2['comments'], $className);
                $this->compare("class", "signature", $classInfo1['signature'], $classInfo2['signature'], $className);
                $this->compare("class", "dependencies", $classInfo1['dependencies'], $classInfo2['dependencies'], $className);


                // properties
                $props1 = $classInfo1['properties'];
                $props2 = $classInfo2['properties'];
                $this->diff("property", "propName", $props1, $props2, $className);
                foreach ($props1 as $propName => $propInfo1) {
                    if (array_key_exists($propName, $props2)) {
                        $propInfo2 = $props2[$propName];
                        $this->compare("property", "comments", $propInfo1['comments'], $propInfo2['comments'], $className, $propName);
                        $this->compare("property", "signature", $propInfo1['signature'], $propInfo2['signature'], $className, $propName);
                    }
                }


                // methods
                $methods1 = $classInfo1['methods'];
                $methods2 = $classInfo2['methods'];
                $this->diff("method", "methodName", $methods1, $methods2, $className);
                foreach ($methods1 as $methodName => $methodInfo1) {
                    if (array_key_exists($methodName, $methods2)) {
                        $methodInfo2 = $methods2[$methodName];
                        $this->compare("method", "comments", $methodInfo1['comments'], $methodInfo2['comments'], $className, $methodName);
                        $identical = $this->compare("method", "signature", $methodInfo1['signature'], $methodInfo2['signature'], $className, $methodName);

                        // parameters
                        if (false === $identical) {
                            $params1 = $methodInfo1['parameters'];
                            $params2 = $methodInfo2['parameters'];
                            $this->diff("parameter", "parameterName", $params1, $params2, $className, $methodName);

                            foreach ($params1 as $paramName => $paramInfo1) {
                                if (array_key_exists($paramName, $params2)) {
                                    $paramInfo2 = $params2[$paramName];
                                    $this->compare("parameter", "name", $paramInfo1['name'], $paramInfo2['name'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "hint", $paramInfo1['hint'], $paramInfo2['hint'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "hasDefaultValue", $paramInfo1['hasDefaultValue'], $paramInfo2['hasDefaultValue'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "reference", $paramInfo1['reference'], $paramInfo2['reference'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "variadic", $paramInfo1['variadic'], $paramInfo2['variadic'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "defaultValue", $paramInfo1['defaultValue'], $paramInfo2['defaultValue'], $className, $methodName, $paramName);
                                    $this->compare("parameter", "position", $paramInfo1['position'], $paramInfo2['position'], $className, $methodName, $paramName);
                                }
                            }
                        }
                    }
                }
            }
        }


    }

    protected function reset()
    {
        $this->removed = [];
        $this->added = [];
        $this->updated = [];
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function diff($scope, $objectName, $v1, $v2, $className = null, $currentName = null)
    {
        switch ($scope) {
            case 'class':
            case 'property':
            case 'method':
            case 'parameter':

                $things1 = array_keys($v1);
                $things2 = array_keys($v2);
                $removed = array_diff($things1, $things2);
                if ($removed) {
                    $this->doRemove($scope, $objectName, $removed, $className, $currentName);
                }

                $added = array_diff($things2, $things1);
                if ($added) {
                    $this->doAdd($scope, $objectName, $added, $className, $currentName);
                }

                break;
            default:
                throw new \LogicException("Unknown scope $scope");
                break;
        }
    }

    private function compare($scope, $objectName, $v1, $v2, $className, $memberName = null, $parameterName = null)
    {
        $ret = true;
        switch ($scope) {
            case 'class':
            case 'property':
            case 'method':
            case 'parameter':
                if ($v1 !== $v2) {
                    $this->doUpdate($scope, $objectName, $v1, $v2, $className, $memberName, $parameterName);
                    $ret = false;
                }
                break;
            default:
                throw new \LogicException("Unknown scope $scope");
                break;
        }

        return $ret;
    }
    

    private function doRemove($scope, $objectName, $removed, $className = null, $currentName = null)
    {
        switch ($scope) {
            case 'class':
                $this->removed[$scope] = $removed;
                break;
            case 'property':
            case 'method':
                array_walk($removed, function (&$v) use ($className) {
                    $v = $className . "::" . $v;
                });
            if (!array_key_exists($scope, $this->removed)) {
                $this->removed[$scope] = [];
            }
                $this->removed[$scope] = array_merge($this->removed[$scope], $removed);
                break;
            case 'parameter':
                array_walk($removed, function (&$v) use ($className, $currentName) {
                    $v = $className . "::" . $currentName . " " . $v;
                });
                if (!array_key_exists($scope, $this->removed)) {
                    $this->removed[$scope] = [];
                }
                $this->removed[$scope] = array_merge($this->removed[$scope], $removed);
                break;
            default:
                throw new \LogicException("Unknown scope: $scope");
                break;
        }
    }

    private function doAdd($scope, $objectName, $added, $className = null, $currentName = null)
    {
        switch ($scope) {
            case 'class':
                $this->added[$scope] = $added;
                break;
            case 'property':
            case 'method':
                array_walk($added, function (&$v) use ($className) {
                    $v = $className . "::" . $v;
                });
                if (!array_key_exists($scope, $this->added)) {
                    $this->added[$scope] = [];
                }
                $this->added[$scope] = array_merge($this->added[$scope], $added);
                break;
            case 'parameter':
                array_walk($added, function (&$v) use ($className, $currentName) {
                    $v = $className . "::" . $currentName . " " . $v;
                });
                if (!array_key_exists($scope, $this->added)) {
                    $this->added[$scope] = [];
                }
                $this->added[$scope] = array_merge($this->added[$scope], $added);
                break;
            default:
                throw new \LogicException("Unknown scope: $scope");
                break;
        }
    }

    private function doUpdate($scope, $objectName, $v1, $v2, $className, $memberName = null, $parameterName = null)
    {
        switch ($scope) {
            case 'class':
                switch ($objectName) {
                    case 'comments':
                    case 'signature':
                    case 'dependencies':
                        $this->updated[$className][$objectName]['v1'] = $v1;
                        $this->updated[$className][$objectName]['v2'] = $v2;
                        break;
                    default:
                        throw new \LogicException("Unknown objectName: $objectName");
                        break;
                }
                break;
            case 'property':
                switch ($objectName) {
                    case 'comments':
                    case 'signature':
                        $this->updated[$className]['properties'][$memberName][$objectName]['v1'] = $v1;
                        $this->updated[$className]['properties'][$memberName][$objectName]['v2'] = $v2;
                        break;
                    default:
                        throw new \LogicException("Unknown objectName: $objectName");
                        break;
                }
                break;
            case 'method':
                switch ($objectName) {
                    case 'comments':
                    case 'signature':
                        $this->updated[$className]['methods'][$memberName][$objectName]['v1'] = $v1;
                        $this->updated[$className]['methods'][$memberName][$objectName]['v2'] = $v2;
                        break;
                    default:
                        throw new \LogicException("Unknown objectName: $objectName");
                        break;
                }
                break;
            case 'parameter':
                switch ($objectName) {
                    case 'name':
                    case 'hint':
                    case 'hasDefaultValue':
                    case 'reference':
                    case 'variadic':
                    case 'defaultValue':
                    case 'position':
                        $this->updated[$className]['methods'][$memberName]["params"][$parameterName][$objectName]['v1'] = $v1;
                        $this->updated[$className]['methods'][$memberName]["params"][$parameterName][$objectName]['v2'] = $v2;
                        break;
                    default:
                        throw new \LogicException("Unknown objectName: $objectName");
                        break;
                }
                break;
            default:
                throw new \LogicException("Unknown scope: $scope");
                break;
        }
    }


}
