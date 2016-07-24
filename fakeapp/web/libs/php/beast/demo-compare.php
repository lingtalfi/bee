<?php



require_once 'alveolus/bee/boot/application-tests.php';


use Bee\Bat\MicroStringTool;
use WebModule\Komin\Beast\BeastEngine\BeastEngine;
use WebModule\Komin\Beast\BeastEngine\Displayer\TestDisplayer;
use WebModule\Komin\Beast\BeastEngine\KobeeBeastEngine;


$o = new BeastEngine();

$p = [
    '"',
    "'",
    ["{", "}"],
];
$a = [
    ['', $p],
    ['abcd efgh', $p],
    ['"abcd efgh', $p],
    ['abc"d efgh', $p],
    ['"abc"d efgh', $p],
    ['"abc\"d efgh', $p],
    ['"abc\\\"d efgh', $p],
    ['"abc\"d" efgh', $p],
    ['"abc\"d" \'e\'fgh', $p],
    ['"abc\"d" \'e\'""fgh', $p],
    ['"abc\"d" \'e\'""""fgh', $p],
    ['"abc\"d" \'e\'""""fgh\'e\'e', $p],
    ['"abc\"d" \'e\'""""fgh\'e"""\'e', $p],
    //
    ['{}', $p],
    ['{doo}', $p],
    ['{doo}{}', $p],
    ['{doo}aa{}', $p],
    ['front-{doo}aa{}', $p],
    ['{front{}}', $p],
    ['{front{a,b,c}}', $p],
    ['hi-{front{a,b,c}}', $p],
    ['hi{a..z}-{front{a,b,c}}', $p],
];
$b = [
    [],
    [],
    [],
    [],
    [
        [0, 4],
    ],
    [],
    [
        [0, 6],
    ],
    [
        [0, 7],
    ],
    [
        [0, 7],
        [9, 11],
    ],
    [
        [0, 7],
        [9, 11],
        [12, 13],
    ],
    [
        [0, 7],
        [9, 11],
        [12, 13],
        [14, 15],
    ],
    [
        [0, 7],
        [9, 11],
        [12, 13],
        [14, 15],
        [19, 21],
    ],
    [
        [0, 7],
        [9, 11],
        [12, 13],
        [14, 156], // [14, 15],
        [19, 24],
    ],
    //
    [[0, 1]],
    [[0, 4]],
    [[0, 4], [5, 6]],
    [[0, 4], [7, 8]],
    [[6, 10], [13, 14]],
    [[0, 8]],
    [[0, 13]],
    [[3, 16]],
    [[2, 7], [9, 22]],
];


$callback = function ($a) {
    return MicroStringTool::getProtectedRangesPos($a[0], $a[1]);
};

$k = new KobeeBeastEngine();
$k->compare($a, $b, $callback, ['focus' => null]);
$d = new TestDisplayer();
$d->display($k);
