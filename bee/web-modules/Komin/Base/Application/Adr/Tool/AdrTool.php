<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\Adr\Tool;

use Bee\Application\Asset\AssetDependencyResolver\AssetCalls\AssetCalls;
use WebModule\Komin\Base\Application\Adr\Stazy\StazyAdr;


/**
 * AdrTool
 * @author Lingtalfi
 * 2015-01-13
 *
 */
class AdrTool
{
    private static $metaCache = null;

    public static function getAdrMeta()
    {
        if (null === self::$metaCache) {
            $s = '';
            $ac = AssetCalls::getInst();
            $assets = StazyAdr::getInst()->resolve($ac->getLibs(), $ac->getAssets());
            foreach ($assets['js'] as $url) {
                $s .= '<script src="' . $url . '"></script>' . PHP_EOL;
            }
            foreach ($assets['css'] as $url) {
                $s .= '<link rel="stylesheet" href="' . $url . '">' . PHP_EOL;
            }
            self::$metaCache = $s;
        }
        return self::$metaCache;
    }


}
