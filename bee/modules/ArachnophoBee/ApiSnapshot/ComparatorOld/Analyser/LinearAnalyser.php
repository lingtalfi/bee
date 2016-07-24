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


/**
 * LinearAnalyser
 * @author Lingtalfi
 * 2015-05-01
 *
 */
class LinearAnalyser extends Analyser
{


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function prepareAnalysis(){
        a($this->observations);
    }
    protected function zprepareAnalysis()
    {
        foreach ($this->observations as $info) {
            list($type, $scope, $objectName, $currentInfo, $arg1, $arg2) = $info;
            list($curClass, $curMethod, $curProp) = $currentInfo;

            $word = $objectName;
            $word2 = $objectName;
            $context = '';
            switch ($objectName) {
                case 'className':
                    $word = 'classes';
                    break;
                case 'methodName':
                    $word = 'methods';
                    $context = "$curClass";
                    break;
                case 'propName':
                    $word = 'class properties';
                    $fromTo = "from";
                    if ('add' === $type) {
                        $fromTo = "to";
                    }
                    $context = PHP_EOL . "($fromTo class $curClass)";
                    break;
                case 'argName':
                    $word = 'method parameters';
                    $word2 = 'method parameter';
                    $fromTo = "from";
                    if ('add' === $type) {
                        $fromTo = "to";
                    }
                    $context = PHP_EOL . "($fromTo method $curClass::$curMethod)";
                    break;
                case 'dependencies':
                    $word2 = 'class dependencies';
                    $fromTo = "in";
                    $context = PHP_EOL . "($fromTo class $curClass)";
                    break;
                case 'signature':
                    $word2 = $scope . " " . $objectName;
                    $fromTo = "in";
                    $context = PHP_EOL . "($fromTo class $curClass::$curMethod)";
                    break;
                default:
                    break;
            }


            
            $word = '<underline>' . $word . '</underline>';
            $word2 = '<underline>' . $word2 . '</underline>';
            
            switch ($type) {
                case 'add':
                    $this->infoMsg("The following $word have been <bold>added</bold>:" . $context . $this->toList($arg1));
                    break;
                case 'remove':
                    $this->criticalMsg("The following $word have been <bold>removed</bold>:" . $context . $this->toList($arg1));
                    break;
                case 'update':
                    $this->criticalMsg("The following $word2 have been <bold>updated</bold>:" . $context . $this->diffList($arg1, $arg2));
                    break;
                default:
                    throw new \RuntimeException("Invalid type: $type");
                    break;
            }
        }
    }


}
