<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MacBee\Component\Rotator;

use Bee\Bat\DateTool;
use Bee\Bat\FileSystemTool;
use Bee\Bat\FileTool;
use Bee\Bat\ZipTool;
use MacBee\Component\Rotator\Exception\RotatorException;
use MacBee\Component\Rotator\Tool\RotatorTool;


/**
 * Rotator
 * @author Lingtalfi
 * 2015-07-03
 *
 * This rotator uses a config file.
 * See conception notes as documentation.
 *
 */
class Rotator implements RotatorInterface
{

    private $config;


    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS RotatorInterface
    //------------------------------------------------------------------------------/
    public function rotate($identifier)
    {
        if (null !== $this->config) {
            if (array_key_exists($identifier, $this->config)) {

                $config = $this->prepareConfig($this->config[$identifier]);
                if (true === $this->checkConditions($config)) {
                    $this->doRotate($identifier, $config);
                }
            }
            else {
                $this->error("identifier $identifier not found in config");
            }
        }
        else {
            $this->error("undefined config");
        }
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config, $check = true)
    {
        $this->config = $config;
        if (true === $check) {
            foreach ($config as $id => $conf) {
                $this->checkConfig($id, $conf);
            }
        }
        return $this;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/

    private function prepareConfig(array $config)
    {
        $newConfig = array_replace_recursive([
            'compression' => 'zip',
            'namingSystem' => 'auto',
            'conditions' => null,
            'conditionMode' => 'or',
            'emptyFile' => false,
        ], $config);


        return $newConfig;
    }


    private function checkConfig($identifier, array $config)
    {
        if (!array_key_exists('backupDir', $config)) {
            $this->error("backupDir key is missing (identifier=$identifier)");
        }
        if (!is_string($config['backupDir'])) {
            $this->error(sprintf("backupDir key must be a string, %s given (identifier=$identifier)", gettype($config['backupDir'])));
        }


        if (!array_key_exists('pathName', $config)) {
            $this->error("pathName key is missing (identifier=$identifier)");
        }
        if (!is_string($config['pathName'])) {
            $this->error(sprintf("pathName key must be a string, %s given (identifier=$identifier)", gettype($config['backupDir'])));
        }
    }

    private function checkConditions(array $config)
    {
        $conditions = $config['conditions'];
        $cMode = strtolower($config['conditionMode']);

        if (null === $conditions) {
            return true;
        }
        elseif (is_array($conditions)) {

            $ret = true;
            if ($conditions) {
                $ret = false;
                foreach ($conditions as $c) {
                    if (true === $this->checkCondition($c)) {
                        $ret = true;
                    }
                    else {
                        if ('and' === $cMode) {
                            return false;
                        }
                    }
                }
            }
            return $ret;
        }
        else {
            $this->error(sprintf("Unknown conditions types: null or array was expected, %s given", gettype($conditions)));
        }
        return false;
    }


    private function checkCondition(array $condition)
    {
        // extend this when needed
        return true;
    }

    private function doRotate($identifier, array $config)
    {
        $this->checkConfig($identifier, $config);

        $pathName = $config['pathName'];
        if (file_exists($pathName)) {

            $filePath = $this->getBackupFileName($identifier, $config);
            $backupDir = $config['backupDir'];
            FileSystemTool::mkdir($backupDir);
            $backupPath = $backupDir . '/' . basename($filePath);


            // effective rotation

            if ('zip' === $config['compression']) {
                $backupPath .= '.zip';
                ZipTool::zip($pathName, $backupPath);
            }
            else {
                copy($pathName, $backupPath);
            }


            // rotating accordingly with nbBackupMax
            $nbBackupMax = $config['nbBackupMax'];
            if (-1 !== $nbBackupMax && $nbBackupMax > 0) {
                $files = scandir($backupDir);
                $n = count($files) - 2; // note: it just count any files, not backup files
                if ($n > $nbBackupMax) {
                    RotatorTool::keepLatestEntries($backupDir, $nbBackupMax);
                }
            }


            // do we empty original file
            if (true === $config['emptyFile']) {
                file_put_contents($pathName, '');
            }


        }
        else {
            $this->error("File (to rotate) not found: $pathName");
        }
    }

    private function getBackupFileName($identifier, array $config)
    {
        $pathName = $config['pathName'];
        if ('auto' === $config['namingSystem']) {
            $suffix = '_' . DateTool::getY4mdDateTime('file');
            $pathName = FileTool::addSaltSuffix($pathName, $suffix);
            
        }
        else {
            $this->error(sprintf("Not implemented yet: namingSystem %s (identifier=$identifier)", $config['namingSystem']));
        }
        return $pathName;
    }

    private function error($m)
    {
        throw new RotatorException($m);
    }
}
