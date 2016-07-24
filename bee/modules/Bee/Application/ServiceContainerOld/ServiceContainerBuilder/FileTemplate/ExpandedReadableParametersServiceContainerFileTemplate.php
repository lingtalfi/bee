<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Application\ServiceContainer\ServiceContainerBuilder\FileTemplate;

use Bee\Application\ServiceContainer\ServicePlainCode\ServicePlainCode;
use Bee\Bat\DateTool;
use Bee\Component\Arrays\ArrayExportUtil\ArrayExportUtil;
use Bee\Component\Template\FileTemplate\FileTemplate;
use Bee\Notation\PhpArray\PrettyArrayExportUtil\PrettyArrayExportUtil;


/**
 * ExpandedReadableParametersServiceContainerFileTemplate
 * @author Lingtalfi
 * 2015-03-08
 *
 */
class ExpandedReadableParametersServiceContainerFileTemplate extends FileTemplate
{

    protected $parameters;
    protected $services2Codes;


    function __construct(
        array $parameters,
        array $services2Codes,
        array $options = []
    )
    {
        $template = __DIR__ . '/tpl/ExpandedReadableParametersServiceContainer.php.tpl';
        parent::__construct($template, $options);
        $this->parameters = $parameters;
        $this->services2Codes = $services2Codes;
    }

    public function getContent(array $tags = [])
    {
        $arrayExporter = new ArrayExportUtil('php');
        $tags = array_replace([
            'class' => 'ExpandedReadableParametersServiceContainerFileTemplate',
            'author' => 'bebot',
            'date' => DateTool::getY4mdDateTime(),
            'parameters' => $arrayExporter->arrayExport($this->parameters, true),
            'services' => $this->prepareServices(),
        ], $tags);
        return parent::getContent($tags);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function prepareServices()
    {
        $methods = [];
        foreach ($this->services2Codes as $address => $code) {
            /**
             * @var ServicePlainCode $code
             */
            $methodName = 'get_service_' . str_replace('.', '_', $address);
            $p = explode("\n", $code->getCode());
            $serviceCode = "\t\t" . implode("\n\t\t", $p);


            $s = <<<DOO
    public function $methodName () { 
$serviceCode
    }
DOO;
            $methods[] = $s;
        }
        return implode("\n\n\n", $methods);
    }

}
