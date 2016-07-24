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

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Application\Adr\Tool\AdrTool;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;
use WebModule\Komin\Kick\Module\ModuleInterface;


/**
 * KickCms
 * @author Lingtalfi
 * 2015-01-13
 *
 */
class KickCms implements KickCmsInterface
{

    protected $modules;
    protected $zones;
    protected $options;


    public function __construct(array $modules = [], array $zones = [], array $options = [])
    {

        $this->modules = $modules;
        $this->zones = $zones;
        $this->options = array_replace([
            'allowPhp' => true,
            'defaultTpl' => null,
        ], $options);
    }

    public function render($tpl = null)
    {
        if (null === $tpl) {
            $tpl = $this->options['defaultTpl'];
        }
        $content = '';
        $tpl = StazyPsnResolver::getInst()->getPath($tpl);
        if (file_exists($tpl)) {


            /**
             * Parsing the template:
             *
             *  - __adrMeta__ is replaced with the AdrMeta
             *  - $myZone$ is replaced with the content of the myZone zone
             *
             */
            if (true === $this->options['allowPhp']) {
                ob_start();
                require_once $tpl;
                $content = ob_get_clean();
            }
            else {
                $content = file_get_contents($tpl);
            }

            $content = str_replace('__adrMeta__', AdrTool::getAdrMeta(), $content);
            $content = preg_replace_callback('!\$([a-zA-Z0-9:-_.]+)\$!', function ($m) {
                $zoneId = $m[1];
                if (false !== $content = $this->renderZoneContent($zoneId)) {
                    return $content;
                }
                return $m[0];
            }, $content);
        }
        else {
            $this->error("tplNotFound", sprintf("template not found: %s", $tpl));
        }
        return $content;
    }

    public function setZone($zoneId, array $moduleIds)
    {
        $this->zones[$zoneId] = $moduleIds;
    }

    public function getZone($zoneId)
    {
        if (array_key_exists($zoneId, $this->zones)) {
            return $this->zones[$zoneId];
        }
        return false;
    }

    public function getZones()
    {
        return $this->zones;
    }

    /**
     * @param $module : callback|ModuleInterface
     */
    public function setModule($moduleId, $module)
    {
        $this->modules[$moduleId] = $module;
    }

    /**
     * @return false|callback|ModuleInterface
     */
    public function getModule($moduleId)
    {
        if (array_key_exists($moduleId, $this->modules)) {
            return $this->modules[$moduleId];
        }
        return false;
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function renderZoneContent($zoneId)
    {
        $ret = false;
        if (array_key_exists($zoneId, $this->zones)) {
            $ret = '';
            $zone = $this->zones[$zoneId];
            foreach ($zone as $modId) {
                if (false !== $c = $this->renderModule($modId)) {
                    $ret .= $c;
                }
                else {
                    $this->error("moduleNotFound", sprintf("module not found: %s", $modId));
                }
            }
        }
        return $ret;
    }

    protected function renderModule($moduleId)
    {
        $ret = false;
        if (array_key_exists($moduleId, $this->modules)) {
            $ret = '';
            $module = $this->modules[$moduleId];
            if ($module instanceof ModuleInterface) {
                $ret .= $module->render();
            }
            elseif (is_callable($module)) {
                $ret .= call_user_func($module);
            }
            else {
                throw new \RuntimeException(sprintf("Unrecognized module type for module %s", $moduleId));
            }
        }
        return $ret;

    }

    protected function error($id, $msg)
    {
        SuperLogger::getInst()->log('Komin.Kick.Cms.' . $id, $msg);
    }

}
