<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\QuickTemplate\QuickTemplate\TemplateContentFinder;

use CrazyBee\QuickTemplate\QuickTemplate\TemplateObject\TemplateObject;
use CrazyBee\QuickTemplate\QuickTemplate\TemplateObject\TemplateObjectInterface;
use CrazyBee\QuickTemplate\QuickTemplate\Tool\QuickTemplateTool;


/**
 * FileSystemTemplateContentFinder
 * @author Lingtalfi
 * 2015-05-28
 *
 */
class FileSystemTemplateContentFinder implements TemplateContentFinderInterface
{

    private $rootDir;
    private $tplExtension;

    public function __construct()
    {
        $this->rootDir = __DIR__ . '/../tpl';
        $this->tplExtension = 'html';
    }

    public static function create()
    {
        return new static();
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS TemplateContentFinderInterface
    //------------------------------------------------------------------------------/
    /**
     * @param $tplId
     * @return TemplateObjectInterface|false
     */
    public function find($tplId)
    {
        if (is_string($tplId)) {
            list($tplName, $tplGroup) = QuickTemplateTool::getTemplateIdInfo($tplId);
            $file = $this->rootDir . "/$tplGroup/$tplName." . $this->tplExtension;
            if (file_exists($file)) {
                return TemplateObject::create()->setContent(file_get_contents($file));
            }
        }
        return false;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
        return $this;
    }

    public function setTplExtension($tplExtension)
    {
        $this->tplExtension = $tplExtension;
        return $this;
    }


}
