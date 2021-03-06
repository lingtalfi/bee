<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Validation\Validator\ValidatorTest;


/**
 * BaseValidatorTest
 * @author Lingtalfi
 * 2015-05-07
 *
 */
abstract class BaseValidatorTest implements ValidatorTestInterface
{

    protected $params;


    public function __construct(array $params = [])
    {
        $this->params = $params;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function getParams()
    {
        return $this->params;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/


}
