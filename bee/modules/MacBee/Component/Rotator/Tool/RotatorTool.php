<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MacBee\Component\Rotator\Tool;


/**
 * RotatorTool
 * @author Lingtalfi
 * 2015-07-03
 *
 */
class RotatorTool
{

    public static function keepLatestEntries($dir, $maxEntries = 10)
    {
        if (file_exists($dir)) {
            $files = scandir($dir);
            $f2Mtime = [];
            if (count($files) > $maxEntries) {
                foreach ($files as $f) {
                    if ('.' !== $f && '..' !== $f) {
                        $path = $dir . '/' . $f;
                        $f2Mtime[$f] = filemtime($path);
                    }
                }

                if ($f2Mtime) {
                    arsort($f2Mtime);
                    $f2Mtime = array_flip($f2Mtime);
                    $toRemove = array_slice($f2Mtime, $maxEntries);
                    foreach($toRemove as $f){
                        $path = $dir . '/' . $f;
                        unlink($path);
                    }
                }
            }
        }
        else {
            throw new \RuntimeException("Dir not found: $dir");
        }
    }
}
