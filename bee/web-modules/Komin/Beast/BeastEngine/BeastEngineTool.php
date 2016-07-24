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

use WebModule\Komin\Beast\BeastEngine\Exception\BeastException;
use WebModule\Komin\Beast\BeastEngine\Exception\BeastNotApplicableException;
use WebModule\Komin\Beast\BeastEngine\Exception\BeastSkipException;


/**
 * BeastEngineTool
 * @author Lingtalfi
 * 2015-01-24
 *
 */
class BeastEngineTool
{


    public static function getTestResultsString(BeastEngineInterface $beast)
    {
        $s = 0;
        $f = 0;
        $e = 0;
        $na = 0;
        $sk = 0;
        foreach ($beast->getResults() as $row) {
            if (array_key_exists(1, $row)) {
                switch ($row[1]) {
                    case 's':
                        $s++;
                        break;
                    case 'f':
                        $f++;
                        break;
                    case 'e':
                        $e++;
                        break;
                    case 'na':
                        $na++;
                        break;
                    case 'sk':
                        $sk++;
                        break;
                    default:
                        throw new \UnexpectedValueException("type must be one of s, f, e, na or sk");
                        break;
                }
            }
            else {
                throw new \InvalidArgumentException("Invalid row, must at least contain the 1 index");
            }
        }
        return sprintf('_BEAST_TEST_RESULTS:s=%d;f=%d;e=%d;na=%d;sk=%d__',
            $s,
            $f,
            $e,
            $na,
            $sk
        );
    }
}
