<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\Util;

use ArachnophoBee\ApiSnapshot\Tool\ApiSnapshotTool;
use ArachnophoBee\PhpToken\TokenFinder\Tool\TokenFinderTool;
use ArachnophoBee\PhpToken\Tool\TokenTool;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;


/**
 * ApiSnapshotUtil
 * @author Lingtalfi
 * 2015-04-29
 *
 */
class ApiSnapshotUtil
{


    public function takeSnapShot($moduleRootDir, $moduleNamespace)
    {
        $classRet = [];
        if (file_exists($moduleRootDir)) {
            Finder::create($moduleRootDir)->files()->extensions('php')->find(function (FinderFileInfo $file) use (&$classRet, $moduleNamespace) {

                $tokens = token_get_all(file_get_contents($file->getRealPath()));
                if (true === $this->isValidClassByTokens($tokens)) {

                    $classes = TokenFinderTool::getClassNames($tokens);
                    $className = $classes[0];
                    require_once $file;
                    $class = new \ReflectionClass($className);
                    $classRet[$className] = $this->getClassInfo($class, $tokens, $moduleNamespace);
                }
            });
        }
        else {
            throw new \InvalidArgumentException("moduleRootDir not found: $moduleRootDir");
        }
        ksort($classRet);
        return [
            'classes' => $classRet,
        ];
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/


    private function getClassInfo(\ReflectionClass $class, array $tokens, $moduleNamespace)
    {

        $ret = [
            'comments' => (string)$class->getDocComment(),
            'signature' => ApiSnapshotTool::getClassSnapshotSignature($class),
            'dependencies' => $this->getExternalDependencies($tokens, $moduleNamespace),
            'properties' => $this->getPropertiesArray($class),
            'methods' => $this->getMethodsArray($class),
        ];

        return $ret;
    }


    private function getMethodsArray(\ReflectionClass $class)
    {
        $ret = [];
        foreach ($class->getMethods() as $method) {
            $ret[$method->name] = [
                'comments' => (string)$method->getDocComment(),
                'signature' => ApiSnapshotTool::getMethodSnapshotSignature($method),
                'parameters' => $this->getParametersInfo($method),
            ];
        }
        ksort($ret);
        return $ret;
    }


    private function getParametersInfo(\ReflectionMethod $method)
    {
        $ret = [];
        foreach ($method->getParameters() as $p) {

            $hint = null;
            $variadic = false;
            if (version_compare(PHP_VERSION, '5.6.0') >= 0 && $p->isVariadic()) {
                $variadic = true;
            }


            if ($p->isArray()) {
                $hint = 'array';
            }
            elseif (null !== $cl = $p->getClass()) {
                $hint = $cl->getName();
            }

            $defVal = null;
            if ($p->isOptional()) {
                $defVal = $p->getDefaultValue();
            }
            $argument = [];
            $argument['name'] = $p->name;
            $argument['hint'] = $hint;
            $argument['hasDefaultValue'] = $p->isOptional();
            $argument['reference'] = $p->isPassedByReference();
            $argument['variadic'] = $variadic;
            $argument['defaultValue'] = $defVal;
            $argument['position'] = $p->getPosition();
            $ret[$p->name] = $argument;
        }
        return $ret;
    }

    private function getPropertiesArray(\ReflectionClass $class)
    {
        $ret = [];
        foreach ($class->getProperties() as $prop) {
            $ret[$prop->name] = [
                'comments' => (string)$prop->getDocComment(),
                'signature' => ApiSnapshotTool::getPropertySnapshotSignature($prop),
            ];
        }
        ksort($ret);
        return $ret;
    }


    private function getExternalDependencies(array $tokens, $moduleNamespace)
    {
        $deps = TokenFinderTool::getUseDependencies($tokens);
        $deps = array_filter($deps, function ($v) use ($moduleNamespace) {
            if (0 === strpos($v, $moduleNamespace . '\\')) {
                return false;
            }
            return true;
        });
        sort($deps);
        return $deps;
    }

    /**
     * sdp0
     */
    private function isValidClassByTokens(array $tokens)
    {
        $nameSpace = 0;
        $class = 0;

        foreach ($tokens as $token) {
            if (TokenTool::match(T_NAMESPACE, $token)) {
                $nameSpace++;
            }
            if (TokenTool::match(T_CLASS, $token)) {
                $class++;
            }
        }
        return (1 === $nameSpace && 1 === $class);
    }

}
