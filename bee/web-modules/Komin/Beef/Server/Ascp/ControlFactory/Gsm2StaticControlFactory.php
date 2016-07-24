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
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;
use WebModule\Komin\Base\Notation\Service\ServiceCallTool\MethodCallTool;

/**
 * Gsm2StaticControlFactory
 * @author Lingtalfi
 * 2015-01-30
 *
 */
class Gsm2StaticControlFactory extends GsmBaseStaticControlFactory
{
    public function __construct(array $options = [])
    {
        $options['jsUseValue'] = false;
        $options['jsUseValidation'] = false;
        $options = array_replace([
            'dispose' => 'table', // table|br
        ], $options);
        return parent::__construct($options);
    }


    protected function getDefaultLabel($controlName)
    {
        return $controlName;
    }
    
 
    
    protected function getTip(array $controlNode)
    {
        if ('table' === $this->options['dispose']) {
            if (array_key_exists('tip', $controlNode)) {
                return $controlNode['tip'];
            }
        }
        else {
            if (array_key_exists('tip', $controlNode)) {
                return '<div class="controltip">' . $controlNode['tip'] . '</div>' . PHP_EOL;
            }
        }
        return false;
    }

    
    
    
    protected function disposeElements($control, $label, $tip)
    {
        $s = '';
        if ('br' === $this->options['dispose']) {
            if ($label) {
                $s .= $label;
            }
            $s .= $control;
            if ($tip) {
                $s .= $tip;
            }
        }
        else { // table?
            if ('table') {


                $s .= '<tr>';
                if ($label && $tip) {
                    $s .= '<td>' . $label . '</td>';
                    $s .= '<td>' . $control . '</td>';
                    $s .= '<td><span style="cursor: pointer" title="' . htmlspecialchars($tip) . '">?</span></td>';
                }
                elseif ($label) {
                    $s .= '<td>' . $label . '</td>';
                    $s .= '<td colspan="2">' . $control . '</td>';
                }
                else {
                    $s .= '<td colspan="3">' . $control . '</td>';
                }
                $s .= '</tr>';
                $s .= '<tr class="beef-error-control" style="display: none"><td colspan="3"></td></tr>';
            }
        }
        return $s;
    }
}
