<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Mac\FileSystem;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;


/**
 * MacFileSystemTool
 * @author Lingtalfi
 * 2014-08-22
 *
 */
class MacFileSystemTool
{

    public static function removeDsStore($dir)
    {
        if (file_exists($dir)) {
            Finder::create($dir)
                ->files()
                ->ignoreSpecials(false)
                ->baseName('!\.DS_Store!', true)
                ->find(function (FinderFileInfo $file) {
                    unlink($file->getRealPath());
                });
        }
    }
}
