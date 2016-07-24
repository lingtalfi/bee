<?php



require_once 'alveolus/bee/boot/application-tests.php';


use Bee\Devil\Diff\StringDiff\StringDiffTool;
use WebModule\Komin\Beast\BeastEngine\BeastEngine;
use WebModule\Komin\Beast\BeastEngine\Displayer\TestDisplayer;
use WebModule\Komin\Beast\BeastEngine\KobeeBeastEngine;


$o = new BeastEngine();


$c = [
    [9, 9],
    [9, 9],
    ['', '',],
    [9, 9],
];
$c2 = [
    ['I love cats', 'cats dogs',],
];
$callback = function ($v) {
    list($before, $lcs) = $v;
    return StringDiffTool::getLcsDiffMap($before, $lcs);
};

$k = new KobeeBeastEngine();
$k->testException($c, $callback, '\InvalidArgumentException', ['focus' => null]);
$k->testException($c2, $callback, '\UnexpectedValueException', ['focus' => null]);
$d = new TestDisplayer();
$d->display($k);
