<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Repository\RepositoryHandler;

use Bee\Bat\FileSystemTool;
use Bee\Component\Log\SuperLogger\Traits\SuperLoggerTrait;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;


/**
 * FileRepositoryHandler
 * @author Lingtalfi
 * 2015-02-15
 *
 *
 * This handler organizes resources as files as following:
 *
 *
 * - $rootDir:
 * ----- $identifier:
 * --------- versions:
 * ------------- $identifier-$version.zip
 *
 * With:
 *      $rootDir: string, the path to an arbitrary root dir
 *      $identifier: string, the resource identifier
 *      $version: string, the resource version
 *
 * This organization allows us to preserver some room for further organization.
 * For instance, we can create a sources folder next to the versions folder if needed.
 *
 *
 *
 *
 */
class FileRepositoryHandler implements RepositoryHandlerInterface
{

    use SuperLoggerTrait;

    protected $rootDir;
    protected $options;

    public function __construct($rootDir, array $options = [])
    {
        $this->options = array_replace([], $options);
        $this->rootDir = StazyPsnResolver::getInst()->getPath($rootDir);
        if (!file_exists($this->rootDir)) {
            if (false === FileSystemTool::mkdir($this->rootDir)) {
                $this->slog("cannotCreateRootDir", sprintf("Cannot create root dir at: %s", $this->rootDir));
                $this->rootDir = false;
            }
        }
    }


    /**
     * @return array
     */
    public function getList()
    {
        $ret = [];
        if (false !== $this->rootDir) {
            Finder::create($this->rootDir)->directories()->maxDepth(0)->find(function (FinderFileInfo $file) use (&$ret) {
                $ret[] = $file->getFilename();
            });
        }
        return $ret;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

}
