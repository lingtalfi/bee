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
 * BeastEngine
 * @author Lingtalfi
 * 2015-01-23
 *
 */
class BeastEngine implements BeastEngineInterface
{

    /**
     * Each result is an array with the following entries:
     *
     * - result: s|f|e|na|sk, the result of the test
     *
     *
     *
     */
    protected $results;
    protected $options;


    protected $numMessage;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'testSuccessMessage' => "ok",
            'testFailureMessage' => "test failed",
            'testErrorMessage' => "An error occurred while executing the test!",
            'testSkipMessage' => "Test was skipped!",
            'testNotApplicable' => "This test is not applicable on this machine!",
        ], $options);
        $this->results = [];
        $this->numMessage = 0;
    }





    //------------------------------------------------------------------------------/
    // IMPLEMENTS BeastEngineInterface
    //------------------------------------------------------------------------------/
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
    public function test($f)
    {
        if (is_callable($f)) {
            $this->numMessage++;
            $this->executeTest($f);
        }
        else {
            throw new \InvalidArgumentException("Invalid argument: f must be a callable");
        }
    }

    /**
     * @return array of:
     *
     *      - 0: test number, starting from 1
     *      - 1: type (s|f|e|sk|na)
     *      - 2: message=""
     *
     *
     */
    public function getResults()
    {
        return $this->results;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function executeTest($f)
    {
        try {
            $msg = null;
            $r = call_user_func_array($f, [&$msg]);
            if (true === $r) {
                if (null === $msg) {
                    $msg = $this->options['testSuccessMessage'];
                }
                $type = 's';
            }
            elseif (false === $r) {
                if (null === $msg) {
                    $msg = $this->options['testFailureMessage'];
                }
                $type = 'f';
            }
            else {
                throw new \RuntimeException("A test must always return a boolean");
            }

        } catch (BeastException $e) {
            if ($e instanceof BeastSkipException) {
                $msg = $e->getMessage();
                if (empty($msg)) {
                    $msg = $this->options['testSkipMessage'];
                }
                $type = 'sk';
            }
            elseif ($e instanceof BeastNotApplicableException) {
                $msg = $e->getMessage();
                if (empty($msg)) {
                    $msg = $this->options['testNotApplicable'];
                }
                $type = 'na';
            }
            else {
                throw new \RuntimeException("A BeastException must either be a skip or notApplicable exception");
            }
        } catch (\Exception $e) {
            $msg = null;
            if (empty($e->getMessage())) {
                $msg = $this->options['testErrorMessage'];
            }
            else {
                $msg = $this->formatExceptionMessage($e);
            }
            $type = 'e';
        }
        $this->registerTestResult($type, $msg);
    }


    protected function registerTestResult($type, $message)
    {
        $this->results[] = [$this->numMessage, $type, $message];
    }


    protected function formatExceptionMessage(\Exception $e)
    {
        $s = '';
        $s .= 'Message: %s' . PHP_EOL;
        $s .= 'File: %s' . PHP_EOL;
        $s .= 'Line: %s' . PHP_EOL;
        $s .= 'Trace: %s' . PHP_EOL;
        // remember that beast is web oriented, so nl2br is okay here
        return nl2br(sprintf($s, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString()));
    }

}
