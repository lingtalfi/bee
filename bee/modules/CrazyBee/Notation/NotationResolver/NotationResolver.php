<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Notation\NotationResolver;

use Bee\Bat\ClassTool;
use Bee\Bat\GuiStringTool;
use Bee\Bat\StringTool;
use Bee\Bat\VarTool;
use Bee\Chemical\Errors\ErrorsTrait;
use Bee\Chemical\Warnings\WarningsTrait;
use CrazyBee\Notation\NotationResolver\NotationFinder\NotationFinder;
use CrazyBee\Notation\NotationResolver\NotationFinder\RecursiveNotationFinderInterface;


/**
 * NotationResolver
 * @author Lingtalfi
 * 2015-05-16
 *
 * Very important note:
 *          always set finder of type container BEFORE the finder of type reference or abbreviation.
 *
 *          That's because a container can contain other expressions, so if we execute containers first,
 *          there will be no conflict.
 *          If you don't do so, you might expect to have conflicts like: cannot inject value of type array in a string...
 *
 *
 *
 */
class NotationResolver implements NotationResolverInterface
{

    use ErrorsTrait, WarningsTrait;


    private $finders;
    /**
     * array of name => list of finder names (php array)
     * or
     * array of name => array callback ( matchingFinderName, recursionLevel )
     *                              returns the list of finder names (php array)
     * or
     * array of name => null  # means no finder at all, equivalent to empty array
     * or
     * array of name => *  # means all finders
     *
     * The list is the list of finder names to re-execute in case of a successful match.
     * If the finder name is not registered, the default is *.
     *
     *
     */
    private $recursiveMap;

    public function __construct()
    {
        $this->finders = [];
        $this->recursiveMap = [];
    }

    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS NotationResolverInterface
    //------------------------------------------------------------------------------/
    public function parseValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->parseValue($v);
            }
        }
        elseif (is_string($value)) {
            $value = $this->doParseString($value, $this->finders, 0);
        }
        return $value;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setFinder(NotationFinder $f, $index = null)
    {
        if (null === $index) {
            $this->finders[] = $f;
        }
        else {
            $this->finders[$index] = $f;
        }
        if ($f instanceof RecursiveNotationFinderInterface) {
            $f->setNotationResolver($this);
        }
        return $this;
    }

    public function unsetFinder($index)
    {
        unset($this->finders[$index]);
        return $this;
    }

    public function setRecursiveMap(array $recursiveMap)
    {
        $this->recursiveMap = $recursiveMap;
        return $this;
    }

    public function getFinders()
    {
        return $this->finders;
    }

    public function setFinders(array $finders)
    {
        $this->finders = [];
        foreach ($finders as $name => $finder) {
            $this->setFinder($finder, $name);
        }
        return $this;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function doParseString($string, array $finders, $recLevel)
    {
        $ret = $string;
        $len = mb_strlen($ret);


        foreach ($finders as $finderName => $f) {
            /**
             * @var NotationFinder $f
             */
            if (false !== $info = $f->find($ret)) {
                $newFinders = $this->getNewFinders($finderName, $recLevel);
                $value = $f->getValue();

                $this->mergeWarnings($finderName, $f);


                $slen = $info[1] - $info[0] + 1;
                if (is_scalar($value)) {

                    $ret = StringTool::replace($string, $info[0], $slen, (string)$value);
                    $ret = $this->doParseString($ret, $newFinders, $recLevel + 1);

                }
                else {
                    $isStandAlone = ($len === $slen);
                    if (true === $isStandAlone) {
                        $ret = $value;
                        // we might want to be able to bypass this reparsing of each element of the array below?
                        if (is_array($ret)) {
                            array_walk_recursive($ret, function (&$v) use ($newFinders, $recLevel) {
                                if (is_string($v)) {
                                    $v = $this->doParseString($v, $newFinders, $recLevel + 1);
                                }
                            });
                        }
                    }
                    else {
                        if (null === $value) {
                            $ret = StringTool::replace($string, $info[0], $slen, (string)$value);
                        }
                        else {
                            $expr = mb_substr($string, $info[0], $slen);
                            $this->addError(sprintf("Trying to inject a value of type %s into a string. Expression: \"%s\", string: \"%s\"", gettype($value), $expr, GuiStringTool::trail($string, 100)));
                        }
                    }
                }
                break;
            }
        }
        return $ret;
    }


    private function getNewFinders($finderName, $recLevel)
    {
        // by default, we  use recursive process
        $finders = $this->finders;
        if (array_key_exists($finderName, $this->recursiveMap)) {
            $map = $this->recursiveMap[$finderName];
            if ('*' === $map) {
                $finders = $this->finders;
            }
            elseif (null === $map) {
                $finders = [];
            }
            elseif (is_array($map)) {
                $finders = [];
                foreach ($map as $fName) {
                    if (array_key_exists($fName, $this->finders)) {
                        $finders[$fName] = $this->finders[$fName];
                    }
                }
            }
            elseif (is_callable($map)) {
                $finders = call_user_func($map, $finderName, $recLevel);
                if (!is_array($finders)) {
                    throw new \InvalidArgumentException(sprintf("callback's return must be of type array, %s given", gettype($finders)));
                }
            }
        }
        return $finders;
    }

    private function mergeWarnings($finderName, NotationFinder $f)
    {
        if ($f->hasWarning()) {
            $arr = $f->getWarnings();
            array_walk($arr, function (&$v) use ($finderName, $f) {
                $v = sprintf("Finder " . $finderName . " (%s): " . $v, ClassTool::getClassShortName($f));
            });
            $this->setWarnings(array_merge($this->getWarnings(), $arr));
        }
    }
}
