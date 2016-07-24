<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Interp;


/**
 * InterpreterListener
 * @author Lingtalfi
 * 2015-06-30
 *
 */
class InterpreterListener implements InterpreterListenerInterface
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS InterpreterListenerInterface
    //------------------------------------------------------------------------------/
    public function info($msg)
    {
        $this->println($msg);
    }

    public function error($msg, $exceptionOrToken = null)
    {
        $this->println($msg);
        if($exceptionOrToken instanceof \Exception){
            a($exceptionOrToken);
        }
        elseif($exceptionOrToken instanceof Token){
            a($exceptionOrToken);
        }
    }


    private function println($m)
    {
        echo $m;
        if ('cli' === PHP_SAPI) {
            echo PHP_EOL;
        }
        echo '<br>';
    }
}
