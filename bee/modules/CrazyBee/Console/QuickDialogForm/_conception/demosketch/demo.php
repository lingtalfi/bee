#!/usr/bin/env php
<?php


use CrazyBee\Console\QuickDialogForm\QuickDialogForm;
use Komin\Component\Console\Dialog\Dialog;
use Komin\Component\Console\InteractiveArray\InteractiveArray;
use Komin\Component\Console\KeyboardListener\Observer\SymbolicCodeObserver\EditableLineSymbolicCodeObserver;
use Komin\Component\Console\KeyboardListener\Tool\KeyboardListenerTool;

require_once 'alveolus/bee/boot/autoload.php';


KeyboardListenerTool::safeStty();





/**
 * Validation des données
 */

$form = [
    ['name', "What's your name:", 'default', 'minLength=2', 'pierre'],
    ['age', "Age:", null, 'int', null],
];
$o = new QuickDialogForm($form, ['labelSuffix' => ' ']);
$r = $o->play();


echo PHP_EOL;
a($r);

