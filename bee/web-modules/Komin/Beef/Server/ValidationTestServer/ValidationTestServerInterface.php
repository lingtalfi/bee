<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\ValidationTestServer;



/**
 * ValidationTestServerInterface
 * @author Lingtalfi
 * 2015-02-08
 * 
 */
interface ValidationTestServerInterface {

    /**
     * $type: js|php
     * @return mixed|false in case of failure,
     *                  a php callback in case of success and if type=php
     *                  a js code (sick(oForm)) binding a validation test to oForm in case of success and if type=js
     * 
     *          Both test types work the same:
     * 
     *                  - true|string    callback ( value, array params )
     *                              Returns true if the test is a success,
     *                              or a translated error message in case of failure.
     *                              The tags are already resolved.
     *                              
     *                  - they throw exception when a param is missing, or when something wrong occurs
     *                  - the name of the test uses camelCase (for instance minLength)
     * 
     * 
     *          The main difference between both types is that js error messages have are translated using php,
     *          so they contain additional tags, wrapped with the dollars symbol, for instance $myErrorMsg$.
     * 
     * 
     * 
     */
    public function getValidationTest($testName, $type);
}
