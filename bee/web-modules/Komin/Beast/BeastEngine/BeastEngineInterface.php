<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\BeastEngine;


/**
 * BeastEngineInterface
 * @author Lingtalfi
 * 2015-01-23
 *
 */
interface BeastEngineInterface
{

    /**
     * @param callable $f
     *
     *             bool  f ( &msg=null )
     *
     *             This callback returns true if the result is a success,
     *                  false if the result is a failure.
     *              It throws a BeastSkipException to skip the test,
     *              a BeastNotApplicableException to raise the not applicable flag,
     *              or any other exception if the result is an error.
     *
     *
     *
     * @return mixed
     */
    public function test($f);

    /**
     * @return array of:
     *
     *      - 0: test number, starting from 1
     *      - 1: type (s|f|e|sk|na)
     *      - 2: message=""
     *
     *
     */
    public function getResults();

}
