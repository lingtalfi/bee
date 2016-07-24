#!/usr/bin/env php
<?php


use CrazyBee\Console\GrungeProgram\GrungeDriver\StepConvertor;
use CrazyBee\Console\GrungeProgram\NotationResolver\GrungeNotationResolver;
use CrazyBee\Console\GrungeProgram\StepProcessor\GrungeStepProcessor;
use CrazyBee\Console\StepDrivenProgram\Environment\Environment;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;
use Komin\Component\Console\KeyboardListener\Observer\SymbolicCodeObserver\EditableLineSymbolicCodeObserver;
use Komin\Component\Console\KeyboardListener\Tool\KeyboardListenerTool;

require_once 'alveolus/bee/boot/autoload.php';


KeyboardListenerTool::safeStty();

class AAA
{
    public function say($msg, StepInterface $step, EnvironmentInterface $e)
    {
        $e->setVariable("mola", $msg);
        echo "say: said $msg";
//        $step->setGoto("coucou");
    }
}

$myObject = new AAA();


$env = Environment::create()->setVariable('harry', 'sa');


$resolver = GrungeNotationResolver::create()
    ->injectProgramCore($myObject)
    ->injectParameters([
        'sapeur' => 'coucou',
    ])
    ->injectSessionVars([
        'doo' => 'banana',
    ]);

$convertor = new StepConvertor();
$convertor->setResolver($resolver);

$step1 = $convertor->convert([
    'head' => "Hello kitty\n",
    'actions' => [
        'boolean' => "Do you like sushis?\n",
        'yes' => [
            'text' => "Apple",
            'actions' => "@p->say(a, @step, @env)",
            'goto' => 'apple',
        ],
        'no' => [
            'text' => "Banana",
            'goto' => '$doo',
        ],
        'default' => 'no',
    ],
    'tail' => "\nBye kitty",
]);


$stepApple = $convertor->convert([
    'head' => "You said apple",
]);

$stepBanana = $convertor->convert([
    'head' => "You said banana, banana",
]);


GrungeStepProcessor::create()
    ->setEnvironment($env)
    ->setResolver($resolver)
    ->registerStep('main', $step1)
    ->registerStep('apple', $stepApple)
    ->registerStep('banana', $stepBanana)
    ->execute();

a($env);


/**
 * Bundle creator
 * Bundle packer
 */