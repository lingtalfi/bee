<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\ValidationTestServer;

use Bee\Bat\ClassTool;
use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;


/**
 * ValidationTestServer
 * @author Lingtalfi
 * 2015-02-08
 *
 */
class ValidationTestServer implements ValidationTestServerInterface
{

    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'jsValidationTestDirs' => [
                '[object]/ValidationTest',
            ],
            'phpValidationTestDirs' => [
                '[object]/ValidationTest',
            ],
        ], $options);
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ValidationTestServerInterface
    //------------------------------------------------------------------------------/
    /**
     * $type: js|php
     * @return mixed|false in case of failure,
     *                  a php callback in case of success and if type=php
     *                  a js code (sick(oForm)) binding a validation test to oForm in case of success and if type=js
     *
     * This implementation accepts translations in js code.
     *      However, the syntaxe is very strict: it has to be in a variable on one line, like this:
     *
     *          var msgFmt = "Please type more than [min] chars ([currentLength] given)"; // @translator
     *
     *
     * The variable name can be changed, but the string must be wrapped with double or single quotes,
     * and must not be interrupted (or concatenated), it must be one single block.
     * The line must end with the @translator tag.
     *
     *
     */
    public function getValidationTest($testName, $type)
    {
        if ('php' === $type) {
            foreach ($this->options['phpValidationTestDirs'] as $dir) {
                $dir = StazyPsnResolver::getInst()->getPath($dir, $this);
                if (file_exists($dir)) {
                    $className = ucfirst($testName) . 'Test';
                    $classFile = $dir . '/' . $className . '.php';
                    if (file_exists($classFile)) {
                        $class = ClassTool::getClassNameByFile($classFile);
                        $o = new $class;
                        return $o;
                    }
                    else {
                        $this->log("phpValidationTestNotFound", sprintf("php validation test not found: %s", $classFile));
                    }
                }
                else {
                    $this->log("phpValidationTestDirNotFound", sprintf("php validation test dir not found: %s", $dir));
                }
            }
        }
        elseif ('js' === $type) {
            foreach ($this->options['jsValidationTestDirs'] as $dir) {
                $dir = StazyPsnResolver::getInst()->getPath($dir, $this);
                if (file_exists($dir)) {
                    $fileName = ucfirst($testName) . 'Test';
                    $file = $dir . '/' . $fileName . '.js';
                    if (file_exists($file)) {
                        $jsCode = file_get_contents($file);
                        $jsCode = preg_replace_callback('!var ([a-zA-Z0-9]+)\s*=\s*"([^"]+)"\s*;\s+//\s*@translator!', function ($m) {
                            $var = $m[1];
                            $translat = $this->translate($m[2]);
                            return 'var ' . $var . ' = "' . str_replace('"', '\\"', $translat) . '";';
                        }, $jsCode);
                        return $jsCode;
                    }
                    else {
                        $this->log("jsValidationTestNotFound", sprintf("js validation test not found: %s", $file));
                    }
                }
                else {
                    $this->log("jsValidationTestDirNotFound", sprintf("js validation test dir not found: %s", $dir));
                }
            }
        }
        else {
            $this->log("unknownType", sprintf('unknown type: %s', $type));
        }
        return false;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log("Komin.Beef.ValidationTestServer." . $id, $msg);
    }

    protected function translate($msgId, $catalogInfo = null, $tags = null, $pluralNumber = null, $lang = null)
    {
        return StazyTranslator::getInst()->translate($msgId, $catalogInfo, $tags, $pluralNumber, $lang);
    }

}
