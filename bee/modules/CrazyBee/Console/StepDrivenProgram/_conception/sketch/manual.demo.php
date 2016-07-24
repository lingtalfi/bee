#!/usr/bin/env php
<?php


use CrazyBee\Console\GrungeProgram\GrungeProgram;
use CrazyBee\Console\GrungeProgram\Step\GrungeStep;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\Step;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;
use CrazyBee\Console\StepDrivenProgram\StepProcessor\StepProcessor;
use Komin\Component\Console\Dialog\Dialog;
use Komin\Component\Console\Dialog\Tool\DialogListTool;
use Komin\Component\Console\KeyboardListener\Observer\SymbolicCodeObserver\EditableLineSymbolicCodeObserver;
use Komin\Component\Console\KeyboardListener\Tool\KeyboardListenerTool;

require_once 'alveolus/bee/boot/autoload.php';


KeyboardListenerTool::safeStty();


$c = StepProcessor::create()->registerSteps([

    'main' => Step::create()
        ->setHead("Bienvenue dans ImageResizer\n")
        ->setAction(function (StepInterface $s, EnvironmentInterface $e) {

            $q = DialogListTool::listToQuestion("What program do you want to run?" . PHP_EOL, [
                'resizeAbsolute',
                'crop',
            ]);
            $d = Dialog::create()->setQuestion($q)->setSubmitCodes('return');


            $val = $d->execute();

            if ('0' === $val) {
                $s->setGoto("resizeAbsolute");
            }
            else {
                $s->setGoto("crop");
            }
            echo PHP_EOL;
        }),
    'resizeAbsolute' => Step::create()
        ->setHead("ResizeAbsolute program, ")
        ->setAction(function (StepInterface $s, EnvironmentInterface $e) {


            $d = Dialog::create()->setQuestion("set width: ")->setSubmitCodes('return');
            $width = $d->execute();

            $e->setVariable('_variables.width', $width);
            $s->setGoto('action.resize');
            echo PHP_EOL;
        }),
    'action.resize' => Step::create()
        ->setActions([
            function (StepInterface $s, EnvironmentInterface $e) {
                $width = $e->getVariable('_variables.width', null);
                if (null === $width) {
                    throw new \LogicException("width not set");
                }
                echo "Resizing image to $width" . PHP_EOL;
                $s->setGoto("end");
            },
        ]),
    'end' => Step::create()->setHead("This is the end"),
]);


$c->execute();


