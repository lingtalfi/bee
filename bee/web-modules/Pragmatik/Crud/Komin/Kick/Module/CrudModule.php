<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Komin\Kick\Module;

use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use WebModule\Komin\Kick\Module\BaseModule;


/**
 * CrudModule
 * @author Lingtalfi
 * 2015-01-12
 *
 */
class CrudModule extends BaseModule 
{


    public function render()
    {
        return $this->absorb(__DIR__ . '/inc/auto.crud.tpl.php');
    }

    
    protected function prepare()
    {
        AssetCalls::getInst()->callLib([
            'ajaxloader',
            'assetloader',
            'jquery',
            'jutil',
            'ajaxtim',
            'uii',
            'pea',
            'beef',
            'beel',
            'crud',
        ]);
    }

}
