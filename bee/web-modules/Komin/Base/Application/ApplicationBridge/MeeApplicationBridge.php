<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge;

use Bee\Bat\ArrayTool;
use WebModule\Komin\Base\Application\ApplicationBridge\DeclarationFunction\PluginAssetsRootDirAndUrlDeclarationFunction;


/**
 * MeeApplicationBridge
 * @author Lingtalfi
 * 2015-02-16
 *
 * Implements komin application bridge convention
 *
 */
class MeeApplicationBridge extends ApplicationBridge
{

    /**
     * @param array $declarations , array of identifier => DeclarationFunctionInterface
     */
    public function __construct(array $params, array $declarations = [])
    {

        $required = [
            'pluginAssetsRootDir',
            'pluginAssetsRootUrl',
        ];
        ArrayTool::checkKeys($required, $params);
        $pluginAssetsRootDir = $params['pluginAssetsRootDir'];
        $pluginAssetsRootUrl = $params['pluginAssetsRootUrl'];

        $declarations = array_replace([
            'pluginAssetsRootDirAndUrl' => new PluginAssetsRootDirAndUrlDeclarationFunction($pluginAssetsRootDir, $pluginAssetsRootUrl),
        ], $declarations);
        parent::__construct($declarations);
    }
}
