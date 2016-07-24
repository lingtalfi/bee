<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Kick\Module;

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;


/**
 * BaseModule
 * @author Lingtalfi
 * 2015-01-12
 *
 */
abstract class BaseModule implements ModuleInterface
{
    protected $params;

    function __construct(array $params = [])
    {
        $this->setParams($params);
        $this->prepare();
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function absorb($file)
    {
        try {

            if (false === function_exists('moduleError')) {
                function moduleError($msg, $display = false, $id = null)
                {
                    $msgId = 'Komin.Base.Application.Module.BaseModule.moduleError';
                    if (null !== $id) {
                        $msgId .= '.' . $id;
                    }
                    SuperLogger::getInst()->log($msgId, $msg);
                    if (true === $display) {
                        echo '<div class="moduleerror">' . $msg . '</div>';
                    }
                }
            }

            $params = $this->params;
            ob_start();
            require_once $file;
            return ob_get_clean();

        } catch (\Exception $e) {
            SuperLogger::getInst()->log('Komin.Base.Application.Module.BaseModule.absorb.exception', $e->getMessage());
            return StazyTranslator::getInst()->translate("An exceptional error occurred with this module", get_called_class());
        }
    }

    /**
     * Original intent is to give the opportunity to call AssetCalls
     */
    protected function prepare()
    {

    }

}
