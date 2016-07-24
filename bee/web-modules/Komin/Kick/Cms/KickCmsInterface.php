<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Kick\Cms;
use WebModule\Komin\Kick\Module\ModuleInterface;


/**
 * KickCmsInterface
 * @author Lingtalfi
 * 2015-01-13
 *
 */
interface KickCmsInterface
{


    public function render($tpl = null);

    public function setZone($zoneId, array $moduleIds);

    /**
     * @return false|array of module ids
     */
    public function getZone($zoneId);

    public function getZones();

    /**
     * @param $module : callback|ModuleInterface
     */
    public function setModule($moduleId, $module);

    /**
     * @return false|callback|ModuleInterface
     */
    public function getModule($moduleId);

}
