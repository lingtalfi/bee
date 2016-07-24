<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\ArachnidParser;

use Bee\Bat\FileSystemTool;
use Bee\Bat\FileTool;
use Bee\Bat\SanitizerTool;


/**
 * ArachnidParser
 * @author Lingtalfi
 * 2015-04-12
 *
 *
 * This parser can store all the steps in files, so that we can study
 * what each step does in greater details.
 * This is mainly useful for debug purposes.
 *
 *
 */
class ArachnidParser extends BaseArachnidParser
{


    protected $debugDir;


    public function getDebugDir()
    {
        return $this->debugDir;
    }


    public function setDebugDir($debugDir)
    {
        $this->debugDir = $debugDir;
    }
    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function onStepContentAfter($stepName, $content)
    {
        /**
         * By default, we place all files at the root of the debugDir,
         * and we name them after the stepName, using the following format:
         *
         * fileName: parse-{stepName}.php
         *
         */
        if (!empty($this->debugDir)) {
            $file = $this->debugDir . '/parse-' . SanitizerTool::sanitizeFileName($stepName) . '.php';
            FileSystemTool::filePutContents($file, $content);
        }
        else {
            throw new \RuntimeException("debugDir must not be null");
        }


    }

}
