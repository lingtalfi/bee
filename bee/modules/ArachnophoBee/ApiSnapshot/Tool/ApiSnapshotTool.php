<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\Tool;

use ArachnophoBee\PhpToken\TokenFinder\Tool\TokenFinderTool;
use Bee\Bat\ArrayTool;
use Bee\Bat\DebugTool;
use Bee\Bat\ReflectionTool;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;
use Bee\Notation\Variable\InlineVariableUtil\Adaptor\PhpDocInlineVariableUtilAdaptor;
use Bee\Notation\Variable\InlineVariableUtil\InlineVariableUtil;


/**
 * ApiSnapshotTool
 * @author Lingtalfi
 * 2015-04-26
 *
 * In this implementation, it is assumed that an object is in a php file,
 * and there is only the object class in this file (with the namespace at the top).
 * See doc for more details.
 *
 *
 */
class ApiSnapshotTool
{
    private static $inlineVarUtil;

    public static function getClassSnapshotSignature(\ReflectionClass $class)
    {
        $words = [];


        if (true === $class->isTrait()) {
            $words[] = 'trait';
        }
        else {
            if (true === $class->isAbstract()) {
                $words[] = 'abstract';
            }
            if (true === $class->isInterface()) {
                $words[] = 'interface';
            }
            else {
                $words[] = 'class';
            }
        }
        $words[] = $class->getName();
        if (false !== $parent = $class->getParentClass()) {
            $words[] = 'extends ' . $parent->getName();
        }

        $interfaces = $class->getInterfaceNames();
        if ($interfaces) {
            $words[] = 'implements';
            sort($interfaces);
            $words[] = implode(', ', $interfaces);
        }

        $traits = $class->getTraitNames();
        if ($traits) {
            $words[] = 'uses';
            sort($traits);
            $words[] = implode(', ', $traits);
        }

        return implode(' ', $words);
    }


    public static function getMethodSnapshotSignature(\ReflectionMethod $method)
    {
        $ret = [];
        $words = [];
        if (true === $method->isStatic()) {
            $words[] = 'static';
        }


        $visibility = 'public';
        if ($method->isPrivate()) {
            $visibility = 'private';
        }
        elseif ($method->isProtected()) {
            $visibility = 'protected';
        }

        $words[] = $visibility;
        $words[] = $method->name;
        $words[] = '(';

        $args = [];
        foreach ($method->getParameters() as $arg) {
            $args[] = ReflectionTool::getParameterAsString($arg);
        }
        if ($args) {
            $words[] = implode(', ', $args);
        }
        $words[] = ')';
        return implode(' ', $words);
    }


    public static function getPropertySnapshotSignature(\ReflectionProperty $prop)
    {
        $words = [];

        $visibility = 'public';
        if ($prop->isPrivate()) {
            $visibility = 'private';
        }
        elseif ($prop->isProtected()) {
            $visibility = 'protected';
        }

        $words[] = $visibility;
        if (true === $prop->isStatic()) {
            $words[] = 'static';
        }
        $words[] = $prop->name;

        $class = $prop->getDeclaringClass();
        $defaultValues = $class->getDefaultProperties();
        $defaultVal = $defaultValues[$prop->name];

        if (null !== $defaultVal) {
            $words[] = '=';
            $words[] = self::getInlineVarUtil()->toString($defaultVal);
        }
        return implode(' ', $words);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private static function getInlineVarUtil()
    {
        if (null === self::$inlineVarUtil) {
            self::$inlineVarUtil = new InlineVariableUtil();
            self::$inlineVarUtil->setAdaptors([
                new PhpDocInlineVariableUtilAdaptor([
                    'arrayContent' => true,
                ]),
            ]);
        }
        return self::$inlineVarUtil;
    }


}
