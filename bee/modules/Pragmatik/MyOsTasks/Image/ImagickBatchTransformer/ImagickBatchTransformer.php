<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pragmatik\MyOsTasks\Image\ImagickBatchTransformer;

use Bee\Bat\FileSystemTool;
use Bee\Bat\VarTool;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;
use CrazyBee\Console\StepDrivenProgram\Environment\EnvironmentInterface;
use Komin\Component\Console\PhpProgram\ShellPhpProgram\ShellPhpProgram;


/**
 * ImagickBatchTransformer
 * @author Lingtalfi
 * 2015-05-20
 *
 * This is a stepDriven program helper
 *
 */
class ImagickBatchTransformer
{


    public function __construct()
    {
        if (false === extension_loaded("imagick")) {
            throw new \RuntimeException("This class will not work because the imagick extension is not available on this machine");
        }
    }

    public static function create()
    {
        return new static();
    }

    public function resizeAbsolute($srcDir, $dstDir, $width, $height, EnvironmentInterface $env)
    {
        $nbFilesResized = 0;
        if (!empty($srcDir)) {
            if (file_exists($srcDir)) {

                $imageExtensions = $env->getVariable('imageExtensions');
                $searchDepth = $env->getVariable('searchDepth');
                if (true === VarTool::checkType($imageExtensions, 'array')) {
                    if (empty($dstDir)) {
                        $dstDir = FileSystemTool::getUniqueResourceBySibling($srcDir);
                    }

                    $f = Finder::create($srcDir)->extensions($imageExtensions)->files();
                    if ($searchDepth !== -1) {
                        $f->maxDepth($searchDepth);
                    }


                    $env->setVariable("realDstDir", $dstDir);

                    $width = (int)$width;
                    $height = (int)$height;
                    foreach ($f as $file) {
                        /**
                         * @var FinderFileInfo $file
                         */
                        $dstFile = $dstDir . '/' . $file->getComponentsPath();
                        $image = new \Imagick($file->getRealPath());
                        $image->thumbnailImage($width, $height);

                        $dirName = dirname($dstFile);
                        FileSystemTool::mkdir($dirName);
                        if (true === $image->writeImage($dstFile)) {
                            $nbFilesResized++;
                        }
                    }
                }
            }
            else {
                throw new \InvalidArgumentException(sprintf("srcDir not found on the file system: %s", $srcDir));
            }
        }
        else {
            throw new \InvalidArgumentException("srcDir argument must not be empty");
        }
        return $nbFilesResized;
    }
}
