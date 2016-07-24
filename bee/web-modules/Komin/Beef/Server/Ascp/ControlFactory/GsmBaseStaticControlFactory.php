<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace WebModule\Komin\Beef\Server\Ascp\ControlFactory;

use Bee\Bat\HtmlTool;
use WebModule\Komin\Base\Db\Pdo\PdoTool;
use WebModule\Komin\Base\Notation\Service\ServiceCallTool\MethodCallTool;

/**
 * GsmBaseStaticControlFactory
 * @author Lingtalfi
 * 2015-01-30
 *
 */
abstract class GsmBaseStaticControlFactory extends Gsm1BaseFactory
{

    protected $options;

    public function __construct(array $options = [])
    {
        $options = array_replace([
            /**
             * Opportunity to fine tune input and label attributes at the item level.
             */
            'decorateRadioItemAttr' => function (array &$inputAttr, array &$labelAttr, $controlName, array $controlNode, $value, $label) {
            },
            'radioItemDisposition' => function ($input, $label) {
                return $label . $input;
            },
            /**
             * Opportunity to fine tune input and label attributes at the item level.
             */
            'decorateCheckboxItemAttr' => function (array &$inputAttr, array &$labelAttr, $controlName, array $controlNode, $value, $label) {
            },
            'checkboxItemDisposition' => function ($input, $label) {
                return $label . $input;
            },
        ], $options);
        parent::__construct($options);
    }


    public function getNodeInfo($controlName, array $controlNode)
    {
        $type = $controlNode['type'];
        $html = '';
        $js = null;
        $ret = null;
        $dependencies = null;


        switch ($type) {
            case 'inputText':
                $html .= $this->getInputTextHtml($controlName, $controlNode);
                break;
            case 'inputHidden':
                $html .= $this->getInputHiddenHtml($controlName, $controlNode);
                break;
            case 'inputPassword':
                $html .= $this->getInputPasswordHtml($controlName, $controlNode);
                break;
            case 'inputRadio':
                $html .= $this->getInputRadioHtml($controlName, $controlNode);
                break;
            case 'inputCheckbox':
                $html .= $this->getInputCheckboxHtml($controlName, $controlNode);
                break;
            case 'select':
                $html .= $this->getSelectHtml($controlName, $controlNode);
                break;
            case 'textarea':
                $html .= $this->getTextareaHtml($controlName, $controlNode);
                break;
            default:
                $ret = false;
                break;
        }


        if (null === $ret) {
            $js = $this->getJsCode($controlName, $controlNode);
            $ret = [
                $html,
                $js,
                $dependencies,
            ];
        }
        return $ret;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getSelectHtml($controlName, array $controlNode)
    {
        $html = '';
        $attr = [];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        $html .= '<select' . HtmlTool::toAttributesString($attr) . '>';
        $params = (array_key_exists('params', $controlNode)) ? $controlNode['params'] : [];
        if (array_key_exists('items', $params)) {
            $items = $params['items'];
        }
        elseif (array_key_exists('_generator', $params)) {
            $args = (array_key_exists('args', $params)) ? $params['args'] : [];
            $items = MethodCallTool::callMethod($params['generator'], $args);
        }
        elseif (array_key_exists('_pdo', $params)) {
            $p = explode(':', $params['_pdo']);
            $items = PdoTool::getItemsFromTable($p[0], $p[1], $p[2]);
        }


        if ($items) {
            $firstItem = current($items);
            if (is_array($firstItem)) {
                // select with groups
                foreach ($items as $groupLabel => $groupItems) {
                    $html .= '<optgroup label="' . htmlspecialchars($groupLabel) . '"></optgroup>';
                    foreach ($groupItems as $value => $label) {
                        $html .= '<option value="' . htmlspecialchars($value) . '">' . $label . '</option>';
                    }
                }
            }
            else {
                // select with simple items
                foreach ($items as $value => $label) {
                    $html .= '<option value="' . htmlspecialchars($value) . '">' . $label . '</option>';
                }
            }
        }

        $html .= '</select>';
        return $this->disposeElements($html, $label, $tip);
    }

    protected function getInputCheckboxHtml($controlName, array $controlNode)
    {
        $html = '';
        $attr = [
            'type' => "checkbox",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        $firstId = $attr['id'];
        unset($attr['id']);


        if (array_key_exists('params', $controlNode)) {
            $params = $controlNode['params'];
            if (array_key_exists('items', $params)) {
                $items = $params['items'];
                foreach ($items as $value => $label) {
                    $inputAttr = $attr;

                    if ($firstId) {
                        $id = $firstId;
                        $firstId = null;
                    }
                    else {
                        $id = $this->getUniqueElementId($controlNode);
                    }
                    $labelAttr = [
                        'for' => $id,
                    ];
                    $inputAttr['id'] = $id;


                    call_user_func_array($this->options['decorateCheckboxItemAttr'], [
                        &$inputAttr,
                        &$labelAttr,
                        $controlName,
                        $controlNode,
                        $value,
                        $label,
                    ]);

                    $label = '<label' . HtmlTool::toAttributesString($labelAttr) . '>' . $this->getText($label, 'label') . '</label>' . PHP_EOL;
                    $input = '<input' . HtmlTool::toAttributesString($inputAttr) . '>' . PHP_EOL;
                    $html .= $this->options['checkboxItemDisposition']($input, $label);
                }
            }

        }
        return $this->disposeElements($html, $label, $tip);
    }

    protected function getInputRadioHtml($controlName, array $controlNode)
    {
        $html = '';
        $attr = [
            'type' => "radio",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        $firstId = $attr['id'];
        unset($attr['id']);
        if (array_key_exists('params', $controlNode)) {
            $params = $controlNode['params'];
            if (array_key_exists('items', $params)) {
                $items = $params['items'];
                foreach ($items as $value => $label) {
                    $inputAttr = $attr;

                    if ($firstId) {
                        $id = $firstId;
                        $firstId = null;
                    }
                    else {
                        $id = $this->getUniqueElementId($controlNode);
                    }
                    $labelAttr = [
                        'for' => $id,
                    ];
                    $inputAttr['id'] = $id;


                    call_user_func_array($this->options['decorateRadioItemAttr'], [
                        &$inputAttr,
                        &$labelAttr,
                        $controlName,
                        $controlNode,
                        $value,
                        $label,
                    ]);

                    $label = '<label' . HtmlTool::toAttributesString($labelAttr) . '>' . $this->getText($label, 'label') . '</label>' . PHP_EOL;
                    $input = '<input' . HtmlTool::toAttributesString($inputAttr) . '>' . PHP_EOL;
                    $html .= $this->options['radioItemDisposition']($input, $label);
                }
            }

        }
        return $this->disposeElements($html, $label, $tip);
    }


    protected function getTextareaHtml($controlName, array $controlNode)
    {
        $attr = [];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        return $this->disposeElements('<textarea' . HtmlTool::toAttributesString($attr) . '></textarea>', $label, $tip);
    }

    protected function getInputTextHtml($controlName, array $controlNode)
    {
        $attr = [
            'type' => "text",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        return $this->disposeElements('<input' . HtmlTool::toAttributesString($attr) . '>', $label, $tip);
    }

    protected function getInputPasswordHtml($controlName, array $controlNode)
    {
        $attr = [
            'type' => "password",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        return $this->disposeElements('<input' . HtmlTool::toAttributesString($attr) . '>', $label, $tip);
    }

    protected function getInputHiddenHtml($controlName, array $controlNode)
    {
        $attr = [
            'type' => "hidden",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        /**
         * Hidden elements don't have label nor tip
         */
        return $this->disposeElements('<input' . HtmlTool::toAttributesString($attr) . '>', null, null);

    }

    protected function prepareDefaultControl($controlName, array $controlNode, array &$attr)
    {
        $ret = [
            0 => $this->getDefaultLabel($controlName), // label
            1 => '', // tip
        ];
        $id = null;
        if (false !== $label = $this->getLabel($controlNode, $id)) {
            $ret[0] = $label;
        }
        if (false !== $tip = $this->getTip($controlNode, $id)) {
            $ret[1] = $tip;
        }
        $attr['name'] = $controlName;
        if (array_key_exists('isScalar', $controlNode) && false === $controlNode['isScalar']) {
            $attr['name'] .= '[]';
        }
        $attr['id'] = $id;
        if (array_key_exists('params', $controlNode)) {
            if (array_key_exists('attr', $controlNode['params']) && is_array($controlNode['params']['attr'])) {
                // attr defined by this instance have precedence
                $attr = array_replace($controlNode['params']['attr'], $attr);
            }
        }
        return $ret;
    }

    protected function getDefaultLabel($controlName)
    {
        return '';
    }

    protected function disposeElements($control, $label, $tip)
    {
        $s = '';
        if ($label) {
            $s .= $label;
        }
        $s .= $control;
        if ($tip) {
            $s .= $tip;
        }
        return $s;
    }

}
