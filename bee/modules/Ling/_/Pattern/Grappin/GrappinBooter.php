<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ling\Pattern\Grappin;



/**
 * GrappinBooter
 * @author Lingtalfi
 * 2015-01-24
 * 
 */
class GrappinBooter {

    public static function boot(){
        if (!function_exists('getGrappinConf')) {
            /**
             * Grappin conf, just copy paste in your new modules.
             */

            function grappinGetAbsolutePath($path)
            {
                $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
                $startSlash = false;
                if (DIRECTORY_SEPARATOR === substr($path, 0, 1)) {
                    $startSlash = true;
                }
                $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
                $absolutes = array();
                foreach ($parts as $part) {
                    if ('.' == $part) continue;
                    if ('..' == $part) {
                        array_pop($absolutes);
                    }
                    else {
                        $absolutes[] = $part;
                    }
                }
                $ret = implode(DIRECTORY_SEPARATOR, $absolutes);
                if (true === $startSlash) {
                    $ret = DIRECTORY_SEPARATOR . $ret;
                }
                return $ret;
            }

            function getGrappinConf($rootDir, $fileName = null)
            {
                $rootDir = grappinGetAbsolutePath($rootDir);
                if (file_exists($rootDir)) {

                    if (null === $fileName) {
                        $fileName = 'conf';
                    }
                    $confFile = $rootDir . '/' . $fileName . '.php';
                    if (file_exists($confFile)) {

                        $_conf = null;
                        require_once $confFile;
                        if (!is_array($_conf)) {
                            throw new \RuntimeException("Your conf file must contain an array");
                        }
                        $ret = $_conf;

                        $_conf = null;
                        $devFile = dirname($rootDir) . '/' . $fileName . '.php';
                        if (file_exists($devFile)) {
                            require_once $devFile;
                        }
                        if (is_array($_conf)) {
                            $ret = array_replace($ret, $_conf);
                        }
                        return $ret;
                    }
                    else {
                        throw new \RuntimeException("Are you kidding me? where is your conf file?");
                    }
                }
                else {
                    throw new \RuntimeException(sprintf("Root dir doesn't exist: %s", $rootDir));
                }
            }
        }

    }
}
