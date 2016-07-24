<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\Module;

use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use WebModule\Komin\Base\Db\Pdo\PdoTool;
use WebModule\Komin\Base\Notation\Service\ServiceCallTool\MethodCallTool;
use WebModule\Komin\Beef\Server\Ascp\BeefServer;
use WebModule\Komin\Beef\Server\Ascp\GsmBeefServer;
use WebModule\Pragmatik\Crud\Util\GsmGenerator\GsmGenerator;
use WebModule\Pragmatik\Crud\Util\GsmGenerator\GsmGeneratorInterface;


/**
 * AutoAdminBeefCrudModule
 * @author Lingtalfi
 * 2015-02-05
 *
 *
 * This object is gsm hybrid:
 *      - we start with a gsm1 file to obtain the type
 *      - other properties like labels, tips, validation are defined (if necessary) from the pcf
 *      - we end with a gsm1 control factory that interprets the (gsm1 + pcf customization)
 * All this mess is located in the getControlNodes method.
 *
 *
 */
class AutoAdminBeefCrudModule extends GsmBeefServer
{
    /**
     * @var GsmGeneratorInterface
     */
    protected $gsmGenerator;
    protected $controlNode;


    public function __construct(array $controlNode, GsmGeneratorInterface $gsmGenerator = null, array $options = [])
    {
        $options = array_replace([
            'cacheDir' => '/tmp/autoadminbeefcrudmodule',
            'nodesFilter' => function (array $nodes) {
                return $nodes;
            },
        ], $options);
        $options['gsmCallback'] = [$this, 'getGsmPath'];
        parent::__construct($options);

        if (null === $gsmGenerator) {
            $gsmGenerator = new GsmGenerator();
        }
        $this->gsmGenerator = $gsmGenerator;
        $this->controlNode = $controlNode;
    }


    public function getGsmPath($formId)
    {
        $cFile = $this->options['cacheDir'] . '/gsm2-' . $formId . '.yml';
        if (!file_exists($cFile)) {
            $p = explode('.', $formId);
            $db = $p[0];
            $table = $p[1];
            BabyYamlTool::write($cFile, $this->gsmGenerator->generate($db, $table));
        }
        return $cFile;
    }


    protected function decorateJsCode(array &$allJsCodes){
        $allJsCodes[] = '
            oForm.addOnStartAfter(function($jForm){
                window.beef.util.stripForm($jForm);
            });
        ';
    }


    protected function getControlNodes($formId)
    {
        $nodes = parent::getControlNodes($formId);
        if (is_array($nodes)) {

            /**
             * Filtering nodes (columns)
             */
            $this->filterNodes($nodes);


            /**
             * Using gsm2 sheets, but the factory understands gsm1...
             * We leverage the pcf power to customize the gsm2 node and turn it into a gsm1
             * (that the factory will interpret better)
             */
            if (array_key_exists('validation', $this->controlNode)) {
                $allRules = $this->controlNode['validation'];
                foreach ($allRules as $controlName => $rules) {
                    if (array_key_exists($controlName, $nodes)) {
                        $nodes[$controlName]['validation'] = $rules;
                    }
                }
            }
            
            
        }
        return $nodes;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function filterNodes(array &$nodes)
    {
        if (is_callable($this->options['nodesFilter'])) {
            $nodes = $this->options['nodesFilter']($nodes);
        }
    }
}
