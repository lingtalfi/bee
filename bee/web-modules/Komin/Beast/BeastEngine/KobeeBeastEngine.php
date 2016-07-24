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

use Bee\Bat\DebugTool;
use Bee\Bat\VarTool;
use WebModule\Komin\Beast\BeastEngine\Debugger\Debugger;
use WebModule\Komin\Beast\BeastEngine\Debugger\DebuggerInterface;
use WebModule\Komin\Beel\ListRenderer\TableListRenderer;


/**
 * KobeeBeastEngine
 * @author Lingtalfi
 * 2015-01-24
 *
 * This class has a debugger that contains some useful messages in case of failure for certain methods:
 *
 * - compare
 *
 */
class KobeeBeastEngine extends BeastEngine implements DebuggableBeastEngineInterface
{

    /**
     * @var DebuggerInterface
     */
    protected $debugger;

    public function __construct(DebuggerInterface $debugger = null, array $options = [])
    {
        if (null === $debugger) {
            $debugger = new Debugger();
        }
        $this->debugger = $debugger;
        parent::__construct($options);
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS DebuggableBeastEngineInterface
    //------------------------------------------------------------------------------/
    public function getDebugger()
    {
        return $this->debugger;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function compare(array $values, array $results, $f, array $options = [])
    {
        if (is_callable($f)) {

            $options = array_replace([
                'debugFailure' => 1, // 0=no, 1=just the first failure message, 2=all failure messages
                'focus' => null,
            ], $options);
            $n = count($values);
            $n2 = count($results);
            if ($n === $n2) {

                $focus = $options['focus'];
                $cmpDebugs = []; // array with 3 entries: n, val, res, exp
                foreach ($values as $v) {
                    $exp = array_shift($results);
                    $this->numMessage++;
                    if (
                        null === $focus ||
                        (null !== $focus && (int)$focus === $this->numMessage)
                    ) {
                        $res = call_user_func($f, $v);
                        $this->executeTest(function (&$msg) use ($v, $res, $exp, &$cmpDebugs, $options) {
                            if ($res === $exp) {
                                $msg = sprintf("Values res and exp are the same, with res=%s and exp=%s", VarTool::toString($res), VarTool::toString($exp));
                                return true;
                            }
                            else {
                                $msg = sprintf("Values res and exp are not the same, with res=%s and exp=%s", VarTool::toString($res), VarTool::toString($exp));
                                if (
                                    (1 === $options['debugFailure'] && empty($cmpDebugs)) ||
                                    2 === $options['debugFailure']
                                ) {
                                    $cmpDebugs[] = [$this->numMessage, $v, $res, $exp];
                                }
                                return false;
                            }
                        });
                    }
                }
                if ($cmpDebugs) {
                    $this->debugger->log($this->renderCompareDebug($cmpDebugs));
                }
            }
            else {
                throw new \RuntimeException(sprintf("The arrays values and results don't have the same number of entries (%d, %d)", $n, $n2));
            }
        }
        else {
            throw new \InvalidArgumentException("Invalid argument: f must be a callable");
        }
    }


    public function testException(array $values, $f, $exceptions, array $options = [])
    {
        if (is_callable($f)) {

            $options = array_replace([
                'focus' => null,
            ], $options);
            $focus = $options['focus'];


            if (!is_array($exceptions)) {
                $exceptions = [$exceptions];
            }
            foreach ($values as $v) {
                $this->numMessage++;
                if (
                    null === $focus ||
                    (null !== $focus && (int)$focus === $this->numMessage)
                ) {
                    $this->executeTest(function (&$msg) use ($f, $v, $exceptions) {
                        $thrownException = null;
                        try {
                            call_user_func($f, $v);
                        } catch (\Exception $e) {
                            foreach ($exceptions as $ex) {
                                if ($e instanceof $ex) {
                                    $thrownException = $e;
                                    break;
                                }
                            }
                            if (null === $thrownException) {
                                throw $e;
                            }
                        }
                        if (null !== $thrownException) {
                            $msg = sprintf("Exception of type %s was thrown", get_class($thrownException));
                        }
                        else {
                            $msg = sprintf("Exception of type %s was not thrown", get_class($thrownException));
                        }
                        return (null !== $thrownException);
                    });
                }
            }
        }
        else {
            throw new \InvalidArgumentException("Invalid argument: f must be a callable");
        }
    }


    //------------------------------------------------------------------------------/
    // DISPLAY METHODS
    //------------------------------------------------------------------------------/
    protected function renderCompareDebug(array $cmpDebugs)
    {
        $list = new TableListRenderer(null, [
            'tableAttr' => [
                'class' => 'beeltable beast-tool-debug-table',
            ],
            'headerColsContent' => [
                '0' => 'id',
                '1' => 'value',
                '2' => 'result',
                '3' => 'expected',
            ],
        ]);
        $dump = function ($content, array $item) {
            ob_start();
            VarTool::dump($content);
            return ob_get_clean();
        };
        $list->setFilters([
            '1' => $dump,
            '2' => $dump,
            '3' => $dump,
        ]);
        $list->setRegularColumns([
            '0',
            '1',
            '2',
            '3',
        ]);

        ob_start();
        echo $this->getDebugCssStyle();
        echo $list->render($cmpDebugs);
        return ob_get_clean();
    }


    protected function getDebugCssStyle()
    {
        return '
                    <style>
                        .beast-tool-debug-table{
                            border-collapse: collapse;
                            text-align: left;
                        }
                        .beast-tool-debug-table,
                        .beast-tool-debug-table tr,
                        .beast-tool-debug-table th,
                        .beast-tool-debug-table td
                        {
                            border: 1px solid black;
                            padding: 5px;
                        }
                    </style>';
    }

}
