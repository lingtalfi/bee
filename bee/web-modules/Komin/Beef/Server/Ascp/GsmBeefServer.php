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
class GsmBeefServer extends BaseBeefServer
{


    public function __construct(array $options = [])
    {
        $options = array_replace([
            /**
             * array of formId => gsmPath (psn).
             * When resolving a gsm path, the gsmPaths array will be looked in first.
             * I.e, a match in gsmPaths has always precedence over the other methods of looking for gsmPaths.
             */
            'gsmPaths' => [],
            /**
             * The gsmCallback is used only if no entry matched in gsmPaths.
             * It returns the path(psn) to a valid gsm file, or null.
             */
            'gsmCallback' => function ($formId) {
            },
        ], $options);
        parent::__construct($options);
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getControlNodes($formId)
    {
        $path = null;
        if (array_key_exists($formId, $this->options['gsmPaths'])) {
            $path = $this->options['gsmPaths'][$formId];
        }
        else {
            $path = call_user_func($this->options['gsmCallback'], $formId);
        }
        if (is_string($path)) {
            $path = StazyPsnResolver::getInst()->getPath($path);
            if (file_exists($path)) {
                return FeeConfig::readFile($path);
            }
            else {
                $this->log('gsmPathNotFound', sprintf("gsm file not found: %s", $path));
            }
        }
        return false;
    }
}
