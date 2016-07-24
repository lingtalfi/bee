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

use Bee\Application\Config\Util\FeeConfig;
use Bee\Bat\HtmlTool;
use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;

/**
 * Gsm1KominDynamicFactory
 * @author Lingtalfi
 * 2015-01-30
 *
 * - $general:
 * ----- params:
 * --------- ?options: string|array,
 *                  If it's an array, it's a js options array (k => v).
 *                  The only problem with this technique is that we cannot write callbacks with it.
 *                  Therefore, we can use the string, which is the path (psn) to a js file that contains
 *                  only one var named options.
 *                  With this second method we unleash the full power of javascript.
 *                  It might be possible for some controls to provide additional tags in the form of $tag$.
 *                  This should be indicated in the control's documentation (here in this comment).
 *
 * - array:
 * ----- value: array|string,
 *                  if it's an array, it's the value directly.
 *                  If it's a string, it can be one of the following types:
 *                      - psn, indicates the path to a config file that contains the array
 *                      - ser, indicates the address of a service and method to call, arguments can be defined in params.callArgs.
 *                      - sta, indicates a static class to call
 *
 *                  The syntax is one of the following:
 *
 *                         - <psn:> <psnPath>
 *                         - <ser:> <serviceAddress> <.> <method>
 *                         - <sta:> <className> <:> <method>
 *
 *
 *
 * ----- params:
 * --------- ?callArgs: array of arguments to pass to the value generator defined in value
 * --------- ?options: string|array
 *
 *
 */
class Gsm1KominDynamicFactory extends Gsm1BaseFactory
{
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
        ], $options);
    }


    public function getNodeInfo($controlName, array $controlNode)
    {
        $type = $controlNode['type'];
        $html = '';
        $js = null;
        $ret = null;
        $dependencies = [];
        $jsTags = [];


        switch ($type) {
            case 'array':
                $dependencies[] = 'beef';
                $html .= $this->getArrayHtml($controlName, $controlNode, $jsTags);
                break;
            default:
                $ret = false;
                break;
        }


        if (null === $ret) {
            $js = $this->prepareJsCode($controlName, $controlNode, $jsTags);
            if (empty($dependencies)) {
                $dependencies = null;
            }
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
    protected function getArrayHtml($controlName, array $controlNode, array &$jsTags)
    {
        $html = '';
        $attr = [
            'data-beef-ignore' => "1",
        ];
        list($label, $tip) = $this->prepareDefaultControl($controlName, $controlNode, $attr);
        $html .= $label;
        if (!array_key_exists('id', $attr)) {
            $attr['id'] = $this->getUniqueElementId($controlNode);
        }
        $jsTags['id'] = $attr['id'];
        $html .= '<ul' . HtmlTool::toAttributesString($attr) . '></ul>';
        $html .= $tip;
        return $html;
    }


    protected function prepareJsCode($controlName, array $controlNode, array $jsTags)
    {
        $jsOptions = 'var options = {};';
        $params = (array_key_exists('params', $controlNode)) ? $controlNode['params'] : [];


        if (array_key_exists('options', $params)) {
            $jsOptions = $params['options'];
            if (is_array($jsOptions)) {
                $jsOptions = 'var options = ' . json_encode($jsOptions) . ';' . PHP_EOL;
            }
            else {
                $jsOptions = StazyPsnResolver::getInst()->getPath($jsOptions);
                if (file_exists($jsOptions)) {
                    $jsOptions = file_get_contents($jsOptions);
                }
                else {
                    $this->log("jsCodePathNotFound", sprintf("Js code path not found: %s", $jsOptions));
                }
            }
        }

        $js = '';
        $type = $controlNode['type'];
        switch ($type) {
            case 'array':

                /**
                 * jsOptions tag is replaced by
                 *          var options = {}; # the object is filled with user options if any
                 */
                $js .= str_replace('$jsOptions$', $jsOptions, '
                
                (function(){ 
                
                        var controlName = "' . htmlspecialchars($controlName) . '";
                        var jUl = $("#' . $jsTags['id'] . '");
                        
                        
                        $jsOptions$ 
                        var arrayOptions = {
                            container: jUl,
                            isDeletable: true,
                            isClosed: function (realPath, key, level) {
                                return (level > 1);
                            },
                            onStructureUpdatedAfter: function (v) {
                                
                            }
                        }; 
                        arrayOptions = $.extend(arrayOptions, options);
                        var oArray = new window.beefSimpleArrayControl(arrayOptions);
                        oForm.setDynamicElement(controlName, oArray);
                       
                })();
');
                break;
            default;
                break;
        }
        if (array_key_exists('value', $controlNode)) {
            $value = $controlNode['value'];
            switch ($type) {
                case 'array':
                    if (is_string($value)) {
                        $tri = substr($value, 0, 3);
                        $val = substr($value, 4);
                        switch ($tri) {
                            case 'psn':
                                $path = StazyPsnResolver::getInst()->getPath($val);
                                if (file_exists($path)) {
                                    $value = FeeConfig::readFile($path);
                                }
                                else {
                                    $this->log('psnFileNotFound', sprintf("psn file not found: %s (%s)", $path, $val));
                                }
                                break;
                            case 'ser':
                                // too lazy to handle logging
                                $p = explode('.', $val);
                                $args = (array_key_exists('callArgs', $params) && is_array($params['callArgs'])) ? $params['callArgs'] : [];
                                $value = call_user_func_array([StazyContainer::getInst()->getService($p[0]), $p[1]], $args);
                                break;
                            case 'sta':
                                $p = explode('::', $val);
                                $args = (array_key_exists('callArgs', $params) && is_array($params['callArgs'])) ? $params['callArgs'] : [];
                                $value = call_user_func_array([$p[0], $p[1]], $args);
                                break;
                            default:
                                $value = [];
                                $this->log("unknownGenerator", sprintf("unknown generator: %s", $tri));
                                break;
                        }
                    }
                    break;
                default:
                    break;
            }

            $js .= 'oForm.setValue("' . htmlspecialchars($controlName) . '", ' . json_encode($value) . ');' . PHP_EOL;
        }
        if (array_key_exists('validation', $controlNode)) {
            $rules = $controlNode['validation'];
            $js .= 'oForm.setValidationRule("' . htmlspecialchars($controlName) . '", ' . json_encode($rules) . ');' . PHP_EOL;
        }
        if ('' === $js) {
            $js = null;
        }
        return $js;
    }


    protected function prepareDefaultControl($controlName, array $controlNode, array &$attr)
    {
        $ret = [
            0 => '', // label
            1 => '', // tip
        ];
        $id = null;
        if (false !== $label = $this->getLabel($controlNode, $id)) {
            $ret[0] = $label;
        }
        if (false !== $tip = $this->getTip($controlNode, $id)) {
            $ret[1] = $tip;
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

    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('Komin.Beef.Server.Ascp.ControlFactory.Gsl1KominDynamicFactory.' . $id, $msg);
    }

}
