<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Notation\NotationResolver\NotationFinder;

use Bee\Bat\BdotTool;


/**
 * BDotArrayNotationFinder
 * @author Lingtalfi
 * 2015-05-16
 *
 * is a notation finder which value are found in a resolved bdot array.
 *
 */
class BDotArrayNotationFinder extends NotationFinder
{

    private $array;

    public function __construct()
    {
        parent::__construct();
        $this->array = [];
    }


    public function getValue()
    {
        $value = parent::getValue();
        $found = false;
        $val = BdotTool::getDotValue($value, $this->array, null, $found);
        if (true === $found) {
            $value = $val;
        }
        else {
            // when this finder doesn't match, 
            // it's a really bad idea to try to revert to the 
            // initial state of the expression,
            // because of the recursion which would enter an infinite loop
            $this->addWarning("Could not resolve the value \"$value\" with the given BDot array");
        }
        return $value;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setArray(array $array)
    {
        $this->array = $array;
        return $this;
    }


}
