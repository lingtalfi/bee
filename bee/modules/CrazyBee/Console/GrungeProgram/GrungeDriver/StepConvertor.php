<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\GrungeDriver;

use CrazyBee\Console\GrungeProgram\NotationResolver\GrungeNotationResolver;
use CrazyBee\Console\GrungeProgram\StepAppearanceDecorator\StepAppearanceDecoratorInterface;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use CrazyBee\Console\StepDrivenProgram\Step\Step;
use CrazyBee\Console\StepDrivenProgram\Step\StepInterface;
use Komin\Component\Console\Dialog\Dialog;
use Komin\Component\Console\Dialog\Tool\BooleanDialogTool;
use Komin\Component\Console\Dialog\Tool\DialogListTool;
use Komin\Component\Console\Dialog\Tool\DialogRepeaterTool;


/**
 * StepConvertor
 * @author Lingtalfi
 * 2015-05-19
 *
 */
class StepConvertor
{

    /**
     * @var GrungeNotationResolver
     */
    private $resolver;

    /**
     * @var StepAppearanceDecoratorInterface
     */
    private $decorator;

    public static function create()
    {
        return new static();
    }

    public function convert(array $step)
    {
        if (null === $this->resolver) {
            throw new \LogicException("Please set the resolver before using the convert method");
        }

        $oStep = Step::create();

        if (array_key_exists('head', $step)) {
            $oStep->setHead($step['head']);
        }


        if (array_key_exists('actions', $step)) {
            $actions = $step['actions'];
            if (!is_array($actions)) {
                $actions = [$actions];
            }

            if (array_key_exists('execute', $actions)) {
                $this->handleExecute($actions, $oStep);
            }


            if (array_key_exists('input', $actions)) {
                $this->processInputAction($actions, $oStep);
            }
            elseif (
                array_key_exists('question', $actions) &&
                array_key_exists('response', $actions)
            ) {
                $this->processQuestionResponseAction($actions, $oStep);
            }
            elseif (
                array_key_exists('boolean', $actions) &&
                array_key_exists('yes', $actions) &&
                array_key_exists('no', $actions)
            ) {
                $this->processBooleanAction($actions, $oStep);
            }
        }
        if (array_key_exists('tail', $step)) {
            $oStep->setTail($step['tail']);
        }
        if (array_key_exists('goto', $step)) {
            $oStep->setGoto($step['goto']);
        }
        $this->decorateStep($oStep);
        return $oStep;
    }

    public function setResolver(GrungeNotationResolver $resolver)
    {
        $this->resolver = $resolver;
        return $this;
    }

    public function setDecorator(StepAppearanceDecoratorInterface $decorator)
    {
        $this->decorator = $decorator;
        return $this;
    }





    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function processInputAction(array $actions, StepInterface $step)
    {

        $step->setAction(function (StepInterface $s, EnvironmentInterface $e) use ($actions) {

            $input = $actions['input'];
            $text = $input['text'];
            $this->decorateByProperty($text, 'input');
            $question = $this->resolve($text);


            $d = Dialog::create()
                ->setQuestion($question)
                ->setSubmitCodes('return');

            $userResult = $d->execute();
            $userResult = $this->handleDefault($actions, $userResult);
            $this->handleStoreAs($actions, $userResult, $e);
            $this->handleGoto($input, $s);
            $this->handleActions($input, $s, $e);
        });
    }

    private function processQuestionResponseAction(array $actions, StepInterface $step)
    {
        $question = $actions['question'];
        $responses = $actions['response'];
        if ($responses) {


            if (array_key_exists('default', $actions)) {
                if (!is_string($actions['default']) || !array_key_exists($actions['default'], $responses)) {
                    $this->configError("the default answer doesn't point to an existing response: " . $actions['default']);
                }
            }

            $step->setAction(function (StepInterface $s, EnvironmentInterface $e) use ($question, $responses, $actions) {


                $items = [];
                foreach ($responses as $k => $v) {
                    if (is_array($v)) {
                        $items[$k] = $v['text'];
                    }
                }
                $this->decorateByProperty($question, 'question');
                $q = DialogListTool::listToQuestion($this->resolve($question), $items);

                $d = Dialog::create()
                    ->setQuestion($q)
                    ->setSubmitCodes('return');


                $userResult = DialogRepeaterTool::repeatToValid($d, function ($userResponse) use ($responses, $actions) {
                    if (
                        array_key_exists($userResponse, $responses) ||
                        ('' === $userResponse && array_key_exists('default', $actions))
                    ) {
                        return true;
                    }
                    return false;
                }, PHP_EOL . "This is an invalid answer, please try again" . PHP_EOL);

                $userResult = $this->handleDefault($actions, $userResult);
                $response = $responses[$userResult];

                $this->handleStoreAs($actions, $userResult, $e);
                $this->handleGoto($response, $s);
                $this->handleActions($response, $s, $e);
            });

        }
        else {
            throw new \RuntimeException("The response array must contain at least one element");
        }
    }


    private function processBooleanAction(array $actions, StepInterface $step)
    {

        $codes = ['y', 'n'];
        if (array_key_exists('default', $actions)) {
            $default = $actions['default'];
            if (is_string($default)) {

                if ('yes' === $default) {
                    $actions['default'] = 'y';
                }
                elseif ('no' === $default) {
                    $actions['default'] = 'n';
                }
            }

            if (!in_array($actions['default'], ['y', 'n'])) {
                $this->configError("The default value for a boolean must be either y or n");
            }
            else {
                $codes[] = 'return';
            }
        }

        $step->setAction(function (StepInterface $s, EnvironmentInterface $e) use ($actions, $codes) {

            $text = $actions['boolean'];
            $this->decorateByProperty($text, 'boolean');
            $question = $this->resolve($text);

            $userResult = BooleanDialogTool::getBoolean($question, 'y', 'n', false, $codes);
            $userResult = $this->handleDefault($actions, $userResult);
            $userResult = ('y' === $userResult) ? 'yes' : 'no';

            $response = $actions[$userResult];


            $storedResult = ('yes' === $userResult) ? true : false;

            $this->handleStoreAs($actions, $storedResult, $e);
            $this->handleGoto($response, $s);
            $this->handleActions($response, $s, $e);
        });
    }


    private function handleStoreAs(array $actions, $result, EnvironmentInterface $e)
    {
        if (array_key_exists('storeAs', $actions)) {
            $e->setVariable($actions['storeAs'], $result);
        }
    }

    private function handleDefault(array $actions, $result)
    {
        if ('' === $result && array_key_exists('default', $actions)) {
            $result = $this->resolve($actions['default'], false);
        }
        return $result;
    }

    private function handleGoto(array $array, StepInterface $step)
    {
        if (array_key_exists('goto', $array)) {
            $step->setGoto($array['goto']);
        }
    }

    private function handleActions(array $array, StepInterface $step, EnvironmentInterface $e)
    {
        if (array_key_exists('actions', $array)) {
            $actions = $array['actions'];
            if (is_string($actions)) {
                $this->handleAction($actions, $step, $e);
            }
            elseif (is_array($actions)) {
                foreach ($actions as $action) {
                    $this->handleAction($action, $step, $e);
                }
            }
            else {
                $this->configError(sprintf("action property must be of type array or string, %s given", gettype($actions)));
            }
        }
    }

    private function handleAction($action, StepInterface $step, EnvironmentInterface $e)
    {
        if (is_string($action)) {
            $this->resolver
                ->injectCallableSpecialVars($step, $e)
                ->parseValue($action);
        }
    }


    private function handleExecute(array $actions, StepInterface $oStep)
    {
        $oStep->setAction(function (StepInterface $s, EnvironmentInterface $e) use ($actions) {
            $executes = $actions['execute'];

            if (is_string($executes)) {
                $executes = [$executes];
            }
            if ($executes) {
                $val = null;
                // only the value of the last callback should be stored
                foreach ($executes as $execute) {
                    $val = $this->resolver
                        ->injectCallableSpecialVars($s, $e)
                        ->parseValue($execute);
                }
                $this->handleStoreAs($actions, $val, $e);
            }
        });
    }

    private function resolve($v, $isString = true)
    {
        $r = $this->resolver->parseValue($v);
        if (true === $isString && !is_string($r)) {
            throw new \InvalidArgumentException(sprintf("result r must be of type string, %s given", gettype($r)));
        }
        return $r;
    }

    private function configError($msg)
    {
        $msg = "ConfigFileError: " . $msg;
        throw new \RuntimeException($msg);
    }

    private function decorateStep(StepInterface $step)
    {
        if (null !== $this->decorator) {
            $this->decorator->decorate($step);
        }
    }

    private function decorateByProperty(&$value, $property)
    {
        if (null !== $this->decorator) {
            $value = $this->decorator->decorateProperty($value, $property);
        }
    }
}
