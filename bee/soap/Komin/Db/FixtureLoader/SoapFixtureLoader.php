<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Soap\Komin\Db\FixtureLoader;
use Bee\Application\ServiceContainer\ServiceContainer\SoapContainer;
use Komin\Component\Db\FixtureLoader\FixtureLoaderInterface;


/**
 * SoapFixtureLoader
 * @author Lingtalfi
 * 2015-05-30
 * 
 */
class SoapFixtureLoader {

    /**
     * @return FixtureLoaderInterface
     */
    public static function get(){
        return SoapContainer::getContainer()->getService('komin.db.fixtureLoader');
    }
}
