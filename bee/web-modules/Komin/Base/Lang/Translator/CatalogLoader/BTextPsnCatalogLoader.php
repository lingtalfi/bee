<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\Translator\CatalogLoader;
use Bee\Component\Lang\Translator\CatalogLoader\BTextFileCatalogLoader;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;


/**
 * BTextPsnCatalogLoader
 * @author Lingtalfi
 * 2014-10-21
 *
 */
class BTextPsnCatalogLoader extends BTextFileCatalogLoader
{


    public function __construct($rootFolder)
    {
        $rootFolder = StazyPsnResolver::getInst()->getPath($rootFolder);
        parent::__construct($rootFolder);
    }


}
