<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\Ascp;

use Bee\Application\Config\Util\FeeConfig;
use Bee\Bat\HtmlTool;
use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;
use WebModule\Komin\Base\Server\Ascp\AjaxServer\AjaxServer;
use WebModule\Komin\Beef\Server\Ascp\ControlFactory\ControlFactoryInterface;
use WebModule\Komin\Beef\Server\ValidationTestServer\ValidationTest\ValidationTestInterface;
use WebModule\Komin\Beef\Server\ValidationTestServer\ValidationTestServer;
use WebModule\Komin\Beef\Server\ValidationTestServer\ValidationTestServerInterface;


/**
 * BeefServer
 * @author Lingtalfi
 * 2015-01-30
 *
 * Services:
 * - getForm
 * ----- input:
 * --------- formId: string
 * --------- ?values: array
 * ----- output:
 * --------- html: string, html code for form
 * --------- js: string (js init via sick(oForm))
 * --------- ?dependencies: array
 *
 *
 *
 */
abstract class BaseBeefServer extends AjaxServer
{

    protected $options;
    /**
     * @var ValidationTestServerInterface
     */
    private $validationTestServer;

    /**
     * @return false|array, the controlNodes corresponding to the given formId,
     *                      or false in case of failure
     */
    abstract protected function getControlNodes($formId);


    public function __construct(array $options = [])
    {

        $this->options = array_replace([
            /**
             * The form tpl is the path (psn) to the template that contains the html
             * for the form tag, without the submit button, because we will normally use
             * the button from the dialog where this form will be injected into.
             * The template can use a placeholder for the controls ($controls$).
             * If formTpl is set to null, the controls will be returned as is, without any form
             * template (you probably don't want that).
             */
            'formTpl' => '[object]/asset/formtpl/defaultformtpl.html',
            /**
             * Array of ControlFactoryInterface
             * Those factories should determine the structure of the controlNode.
             */
            'controlFactories' => [],
            /**
             * @param htmlWrap : null|callback,
             *                  if it's a callback, it returns the wrapped html code.
             *                          string callback( htmlCode )
             *
             */
            'htmlWrap' => null,
            /**
             * The tag used to wrap the different controls of the form.
             * Set this to null to NOT wrap the controls.
             */
            'controlWrapTag' => 'div',
            /**
             * If controlWrapTag is not null only, then the controlWrapTagAttributes property
             * will define the html attributes of that tag.
             * It can be either an array of $attrName => $attrValue,
             * or a callback that returns that same array.
             *
             */
            'controlWrapTagAttributes' => function ($controlName, array $controlNode) {
                $ret = [];
                $class = 'control';
                // we don't known for sure the gsm1 (version 1) will be used.
                // therefore we check the existence of the type key first.
                if (array_key_exists('type', $controlNode)) {
                    $class .= ' type_' . $controlNode['type'];
                }
                $ret['class'] = $class;
                return $ret;
            },
            'validationTestServer' => null,
            /**
             * available tags are:
             *  - [controlName]
             *  - [message]
             */
            'validationErrMsgFmt' => '[message]',
        ], $options);
    }


    /**
     * @return mixed|false, false on failure, in which case errors should be set.
     *                      mixed in case of success.
     */
    protected function doExecute($serviceId, array $params = [])
    {
        switch ($serviceId) {
            case 'getForm':
                return $this->getForm($params);
                break;
            default:
                $this->error(sprintf("Unknown service %s", $serviceId));
                break;
        }
        return false;
    }

    protected function getForm(array $params)
    {
        $allDeps = [];
        $allHtmlCodes = [];
        $allJsCodes = [];
        $allJsValidationCodes = [];


        $htmlCode = '';
        if (array_key_exists('formId', $params)) {
            $formId = $params['formId'];


            $vserver = $this->getValidationTestServer();

            if (false !== $controlNodes = $this->getControlNodes($formId)) {

                foreach ($controlNodes as $controlName => $controlNode) {

                    $this->addJsValidationTestAndRulesByControlNode($controlName, $controlNode, $vserver, $allJsValidationCodes);


                    /**
                     * generating the codes (html, js, deps) for each control
                     */
                    $info = null;
                    foreach ($this->options['controlFactories'] as $factory) {
                        /**
                         * @var ControlFactoryInterface $factory
                         */
                        if (false !== $info = $factory->getNodeInfo($controlName, $controlNode)) {
                            break;
                        }
                    }
                    if (is_array($info)) {
                        /**
                         * It's a good idea to wrap the controls here (rather than from the factories),
                         * because it's centralized.
                         */
                        list($html, $js, $deps) = $info;
                        $this->wrapControl($controlName, $controlNode, $html);
                        $allHtmlCodes[] = $html;
                        if (null !== $js) {
                            $allJsCodes[] = $js;
                        }
                        if (null !== $deps) {
                            $allDeps = array_merge($allDeps, $deps);
                        }
                    }
                    else {
                        $this->log("controlNotFound", sprintf("No factory could generate the control: %s", $controlName), true);
                    }


                }
                /**
                 * Now we need to inject the controls in the formTpl
                 */
                $htmlCode = implode(PHP_EOL, $allHtmlCodes);
                if (is_callable($this->options['htmlWrap'])) {
                    $htmlCode = $this->options['htmlWrap']($htmlCode);
                }


                if (null !== $formTpl = $this->options['formTpl']) {
                    $realFormTpl = StazyPsnResolver::getInst()->getPath($formTpl, $this);
                    if (file_exists($realFormTpl)) {
                        $htmlCode = str_replace('$controls$', PHP_EOL . $htmlCode . PHP_EOL, file_get_contents($realFormTpl));
                    }
                    else {
                        $this->log("tplNotFound", sprintf("Template not found: %s (%s)", $realFormTpl, $formTpl));
                    }
                }
            }
            else {
                $this->log('controlNodeNotFound', sprintf("No controlNode found with formId: %s", $formId));
            }

        }
        else {
            $this->missingParams('formId');
        }


        /**
         * Values injection ?
         */
        if (array_key_exists('values', $params) && is_array($params['values'])) {
            /**
             * Filtering values
             */
            $values = $params['values'];
            foreach ($values as $k => $v) {
                if (!array_key_exists($k, $controlNodes)) {
                    unset($values[$k]);
                }
            }

            $s = '';
            $s .= 'var values = ' . json_encode($values) . ';' . PHP_EOL;
            $s .= 'oForm.setValues(values);' . PHP_EOL;
            $allJsCodes[] = $s;
        }


        if ($allJsValidationCodes) {
            foreach ($allJsValidationCodes as $code) {
                array_push($allJsCodes, $code);
            }
        }

        $this->decorateJsCode($allJsCodes);

        $ret = [
            'html' => $htmlCode,
            'js' => implode(PHP_EOL, $allJsCodes),
        ];
        if ($allDeps) {
            $allDeps = array_unique($allDeps);
            $ret['dependencies'] = $allDeps;
        }
        return $ret;
    }

    protected function decorateJsCode(array &$allJsCodes)
    {

    }


    /**
     * Performs a php validation.
     *
     * @return true|array in case of errors:
     *              The errors array's keys
     *              are control names, and the
     *              values is an array of
     *              validationRuleName => error message
     *
     */
    public function validate($formId, array $values)
    {
        $errors = [];
        $rules = $this->getValidationRules($formId);
        foreach ($rules as $cName => $rParams) {
            foreach ($rParams as $ruleName => $params) {
                if (array_key_exists($cName, $values)) {
                    $value = $values[$cName];
                }
                else {
                    $value = null; // checkboxes?
                }
                if (false !== $vtest = $this->getValidationTestServer()->getValidationTest($ruleName, 'php')) {
                    /**
                     * @var ValidationTestInterface $vtest
                     */
                    if (true !== $errMsg = $vtest->execute($value, $params)) {

                        if (!array_key_exists($cName, $errors)) {
                            $errors[$cName] = [];
                        }

                        $errors[$cName][] = str_replace([
                            '[controlName]',
                            '[message]',
                        ], [
                            $cName,
                            $errMsg,
                        ], $this->options['validationErrMsgFmt']);
                    }
                }
                else {
                    $this->log("validationTestNotFound", sprintf("validation test not found: %s", $ruleName));
                }
            }
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function addJsValidationTestAndRulesByControlNode(
        $controlName,
        array $controlNode,
        ValidationTestServerInterface $validationTestServer,
        array &$jsCodes
    )
    {
        if (array_key_exists('validation', $controlNode)) {
            $rules = $controlNode['validation'];
            foreach ($rules as $ruleName => $params) {
                if (false !== $js = $validationTestServer->getValidationTest($ruleName, 'js')) {
                    if (false === array_key_exists($ruleName, $jsCodes)) {
                        $jsCodes[$ruleName] = $js . PHP_EOL;
                    }
                }
                else {
                    $this->log("jsValidationTestNotFound", sprintf("js validation test not found: %s", $ruleName));
                }
            }
            $s = 'oForm.setValidationRule( "' . str_replace('"', '\\"', $controlName) . '", ' . json_encode($rules) . ' );' . PHP_EOL;
            $jsCodes[] = $s;
        }
    }


    protected function wrapControl($controlName, $controlNode, &$controlHtml)
    {
        if (null !== $this->options['controlWrapTag']) {
            $tag = $this->options['controlWrapTag'];
            $attr = $this->options['controlWrapTagAttributes'];
            if (is_callable($attr)) {
                $attr = call_user_func($attr, $controlName, $controlNode);
            }
            if (is_array($attr)) {
                $controlHtml = '<' . $tag . HtmlTool::toAttributesString($attr) . '>' . PHP_EOL . $controlHtml . PHP_EOL . '</' . $tag . '>';
            }
            else {
                $this->log('wrongAttrFormat', sprintf("Wrong attribute format, must be an array, %s given", gettype($attr)));
            }
        }
    }


    protected function error($msg)
    {
        $msg = 'BeefServer: ' . $msg;
        $this->errors[] = $msg;
        return false;
    }

    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('Komin.Beef.Server.' . $id, $msg);
    }

    protected function missingParams($param)
    {
        if (!is_array($param)) {
            $param = [$param];
        }
        $this->log("missingParam", sprintf("The following params are missing: %s", implode(',', $param)));
    }


    protected function getValidationRules($formId)
    {
        $ret = [];
        if (false !== $controlNodes = $this->getControlNodes($formId)) {
            foreach ($controlNodes as $controlName => $controlNode) {
                if (array_key_exists('validation', $controlNode)) {
                    $ret[$controlName] = $controlNode['validation'];
                }
            }
        }
        return $ret;
    }

    protected function getValidationTestServer()
    {
        if (null === $this->validationTestServer) {
            $this->validationTestServer = $this->options['validationTestServer'];
            if (!$this->validationTestServer instanceof ValidationTestServerInterface) {
                $this->validationTestServer = new ValidationTestServer();
            }
        }
        return $this->validationTestServer;
    }

}
