<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Soap\Komin\Db\Pdo\PdoInstances;
use Bee\Application\ServiceContainer\ServiceContainer\SoapContainer;
use Bee\Component\Db\PdoInstances\PdoInstancesInterface;


/**
 * SoapPdoInstances
 * @author Lingtalfi
 * 2015-05-29
 * 
 */
class SoapPdoInstances {


    /**
     * @return PdoInstancesInterface
     */
    public static function get(){
        return SoapContainer::getContainer()->getService('komin.db.pdo.pdoInstances');
    }
}
