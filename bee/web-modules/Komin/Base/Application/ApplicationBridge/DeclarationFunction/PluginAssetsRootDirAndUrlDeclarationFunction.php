<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge\DeclarationFunction;

use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;


/**
 * PluginAssetsRootDirAndUrlDeclarationFunction
 * @author Lingtalfi
 * 2015-02-16
 *
 * Defined in:
 *      komin application bridge convention
 *
 */
class PluginAssetsRootDirAndUrlDeclarationFunction extends BaseDeclarationFunction
{

    protected $pluginAssetsRootDir;
    protected $pluginAssetsRootUrl;

    public function __construct($pluginAssetsRootDir, $pluginAssetsRootUrl)
    {
        $this->pluginAssetsRootDir = StazyPsnResolver::getInst()->getPath($pluginAssetsRootDir);
        $this->pluginAssetsRootUrl = $pluginAssetsRootUrl;
    }


    public function execute(array $params)
    {
        $pluginAbsoluteId = (array_key_exists('pluginAbsoluteId', $params)) ? '/' . $params['pluginAbsoluteId'] : "";
        return [
            $this->pluginAssetsRootDir . $pluginAbsoluteId,
            $this->pluginAssetsRootUrl . $pluginAbsoluteId,
        ];
    }


}
