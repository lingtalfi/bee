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
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;

/**
 * Gsm1BaseFactory
 * @author Lingtalfi
 * 2015-01-30
 *
 */
abstract class Gsm1BaseFactory implements ControlFactoryInterface
{

    protected static $cpt = 0;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'jsUseValue' => true,
            'jsUseValidation' => true,
        ], $options);
    }

    /**
     * Opportunity to translate here (labels, tips, items for radio/checkboxes/select)
     * type=label|tip
     */
    protected function getText($text, $type)
    {
        if (null !== $translator = StazyTranslator::getInst(true)) {
            return $translator->translate($text, $this);
        }
        return $text;
    }

    protected function getJsCode($controlName, $controlNode)
    {
        $js = '';
        if (true === $this->options['jsUseValue'] && array_key_exists('value', $controlNode)) {
            $value = $controlNode['value'];
            $js .= 'oForm.setValue("' . htmlspecialchars($controlName) . '", ' . json_encode($value) . ');' . PHP_EOL;
        }
        if (true === $this->options['jsUseValidation'] && array_key_exists('validation', $controlNode)) {
            $rules = $controlNode['validation'];
            $js .= 'oForm.setValidationRule("' . htmlspecialchars($controlName) . '", ' . json_encode($rules) . ');' . PHP_EOL;
        }
        if ('' === $js) {
            $js = null;
        }
        return $js;
    }


    protected function getLabel(array $controlNode, &$id = null)
    {
        if (array_key_exists('label', $controlNode)) {
            $id = $this->getUniqueElementId($controlNode);
            return '<label for="' . htmlspecialchars($id) . '">' . $this->getText($controlNode['label'], 'label') . ': </label>' . PHP_EOL;
        }
        return false;
    }

    protected function getTip(array $controlNode)
    {
        if (array_key_exists('tip', $controlNode)) {
            return '<div class="controltip">' . $controlNode['tip'] . '</div>' . PHP_EOL;
        }
        return false;
    }

    protected function getUniqueElementId(array $controlNode)
    {
        return '_control_uniqid_' . self::$cpt++;
    }

}
