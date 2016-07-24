<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\QuickDialogForm;


use Bee\Notation\String\ShortCode\Tool\ShortCodeTool;
use CrazyBee\Console\QuickDialogForm\Field\QuickDialogFormField;
use Komin\Component\Console\Dialog\Dialog;
use Komin\Component\Console\Dialog\Tool\DialogRepeaterTool;
use Komin\Component\Console\KeyboardListener\Observer\SymbolicCodeObserver\Tool\EditableLineDefaultValueWrapper;
use Komin\Component\Console\KeyboardListener\Observer\SymbolicCodeObserver\Tool\EditableLineShortcutWrapper;
use Komin\Notation\String\MiniMl\Tool\MiniMlTool;


/**
 * QuickDialogForm
 * @author Lingtalfi
 * 2015-05-09
 *
 */
class QuickDialogForm
{

    private $fields;
    private $options;
    private $validationErrMsg;

    public function __construct(array $fields = [], array $options = [])
    {
        $this->setFields($fields);
        $this->options = array_replace([
            'labelSuffix' => ' ',
        ], $options);
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }


    public function play()
    {
        $ret = [];

        $n = 0;
        foreach ($this->fields as $field) {
            if (0 !== $n) {
                echo PHP_EOL;
            }
            $this->playField($field, $ret);
            $n++;
        }


        return $ret;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function playField($field, array &$ret)
    {
        $this->checkField($field);
        $f = new QuickDialogFormField($field);
        $d = $this->getDialog($f);
        $d->setQuestion($f->getLabel() . $this->options['labelSuffix']);
        $this->addValidation($d, $f);


        if ('' !== $f->getDefaultValue()) {
            EditableLineDefaultValueWrapper::wrap($d->getDialogKeyboardListener()->getEditableLineObserver(), $f->getDefaultValue());
        }
        EditableLineShortcutWrapper::wrap($d->getDialogKeyboardListener()->getEditableLineObserver());

        $value = DialogRepeaterTool::repeatToValid($d, function ($r) use ($d) {
            $ret = true;
            $validations = $d->_validations;
            foreach ($validations as $info) {

                list($callback, $errMsg) = $info;
                if (false === call_user_func($callback, $r)) {
                    $ret = false;
                    $this->validationErrMsg = $errMsg;
                    break;
                }
            }
            return $ret;

        }, function () {
            $msg = MiniMlTool::format($this->validationErrMsg);
            return PHP_EOL . $msg . PHP_EOL;
        });


        $ret[$f->getName()] = $value;
    }

    private function checkField($field)
    {
        if (!is_array($field)) {
            throw new \InvalidArgumentException(sprintf("field argument must be of type array, %s given", gettype($field)));
        }
    }

    /**
     * @return Dialog
     */
    private function getDialog(QuickDialogFormField $f)
    {
        $dialogType = $f->getDialogType();
        if (null === $dialogType) {
            $dialogType = 'default';
        }

        $d = null;
        switch ($dialogType) {
            case 'default':
                $d = Dialog::create();
                break;
            default:
                throw new \RuntimeException("Unknown dialogType: $dialogType");
                break;
        }
        $d->setSubmitCodes('return');
        return $d;
    }

    private function addValidation(Dialog $d, QuickDialogFormField $f)
    {
        $d->_validations = [];

        $validation = $f->getValidationInfo();
        if (null !== $validation) {
            if (is_string($validation)) {
                if ('int' === $validation) {
                    $d->_validations[] = [
                        function ($r) {
                            if (preg_match('!^[0-9]+$!', $r)) {
                                return true;
                            }
                            return false;
                        },
                        "<red>The value must be an integer</red>",
                    ];
                }
                else {
                    $params = ShortCodeTool::parse($validation);
                    if (array_key_exists('minLength', $params)) {
                        $minLength = $params['minLength'];

                        $d->_validations[] = [
                            function ($r) use ($minLength) {
                                if (strlen($r) >= $minLength) {
                                    return true;
                                }
                                return false;
                            },
                            "<red>This field must contain at least $minLength chars</red>",
                        ];
                        unset($params['minLength']);
                    }

                    if (count($params) > 0) {
                        throw new \RuntimeException("There are some params that I don't know about...: " . implode(', ', array_keys($params)));
                    }

                }
            }
            else {
                throw new \InvalidArgumentException(sprintf("validation argument must be of type string, %s given", gettype($validation)));
            }
        }
    }

}
