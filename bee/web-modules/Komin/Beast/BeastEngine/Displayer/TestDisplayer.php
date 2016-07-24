<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beast\BeastEngine\Displayer;

use WebModule\Komin\Beast\BeastEngine\BeastEngineInterface;
use WebModule\Komin\Beast\BeastEngine\BeastEngineTool;
use WebModule\Komin\Beast\BeastEngine\DebuggableBeastEngineInterface;
use WebModule\Komin\Beel\ListRenderer\TableListRenderer;


/**
 * TestDisplayer
 * @author Lingtalfi
 * 2015-01-24
 *
 * @plugins beel
 *
 */
class TestDisplayer implements TestDisplayerInterface
{

    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'showTrace' => true,
            'showDebug' => true,
        ], $options);
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS TestDisplayerInterface
    //------------------------------------------------------------------------------/
    public function display(BeastEngineInterface $beastEngine)
    {
        $testString = BeastEngineTool::getTestResultsString($beastEngine);
        echo $testString;


        if (true === $this->options['showDebug'] && $beastEngine instanceof DebuggableBeastEngineInterface) {
            echo $this->renderDebug($beastEngine);
        }

        if (true === $this->options['showTrace']) {
            echo $this->renderTrace($beastEngine);
        }
    }
    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function renderDebug(DebuggableBeastEngineInterface $beastEngine)
    {
        $logs = $beastEngine->getDebugger()->getLogs();
        ob_start();
        foreach ($logs as $log) {
            echo $log;
        }
        return ob_get_clean();
    }

    protected function renderTrace(BeastEngineInterface $beastEngine)
    {
        $list = new TableListRenderer(null, [
            'tableAttr' => [
                'class' => 'beeltable beast-tool-results-table',
            ],
            'headerColsContent' => [
                '0' => 'id',
                '1' => 'type',
                '2' => 'msg',
            ],
            'lineAttr' => function ($item, $i) {
                return [
                    'class' => 'type' . $item[1],
                ];
            },
        ]);
        $list->setRegularColumns([
            '0',
            '1',
            '2',
        ]);

        ob_start();
        echo $this->getTraceCssStyle();
        echo $list->render($beastEngine->getResults());
        return ob_get_clean();
    }


    protected function getTraceCssStyle()
    {
        return '
                    <style>
                        .beast-tool-results-table{
                            border-collapse: collapse;
                            text-align: left;
                        }
                        .beast-tool-results-table,
                        .beast-tool-results-table tr,
                        .beast-tool-results-table th,
                        .beast-tool-results-table td
                        {
                            border: 1px solid black;
                            padding: 5px;
                        }
                        .beast-tool-results-table .types{
                            background: green;
                        }
                        .beast-tool-results-table .typef{
                            background: red;
                        }
                        .beast-tool-results-table .typee{
                            background: black;
                            color: yellow;
                        }
                        .beast-tool-results-table .typena{
                            background: orange;
                        }
                        .beast-tool-results-table .typesk{
                            background: white;
                        }
                    </style>';
    }
}
