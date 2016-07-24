<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\QuickTemplate;

use Bee\Bat\ArrayTool;
use Bee\Bat\TagTool;
use Bee\Chemical\Errors\Voles\VersatileErrorsTrait;
use CrazyBee\QuickTemplate\QuickTemplate\TemplateContentFinder\FileSystemTemplateContentFinder;
use CrazyBee\QuickTemplate\QuickTemplate\TemplateContentFinder\TemplateContentFinderInterface;


/**
 * QuickTemplate
 * @author Lingtalfi
 * 2015-05-28
 *
 *
 * tplId:
 *      <tplName> (<:> <tplGroup>)?
 *
 *
 * By default, the tplGroup is default.
 *
 *
 *
 */
class QuickTemplate implements QuickTemplateInterface
{

    use VersatileErrorsTrait;

    private $defaultTplGroup;

    /**
     * @var TemplateContentFinderInterface
     */
    private $tplFinder;

    public function __construct()
    {
        $this->defaultTplGroup = 'default';
        $this->setErrorMode('quiet');
    }

    public static function create()
    {
        return new static();
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS QuickTemplateInterface
    //------------------------------------------------------------------------------/
    /**
     * @return string
     */
    public function render($tplId, array $tags = [])
    {
        $s = '';
        if (false !== $tplContent = $this->getTplContent($tplId)) {

            // for now, let's assume that all templates use the same technique:
            // {thisIsATag}
            // a tag's allowed chars are [a-zA-Z0-9_]

            $tags = TagTool::tagify($tags);
            $s = str_replace(array_keys($tags), array_values($tags), $tplContent);

        }
        else {
            $this->error("could not find template content with tplId $tplId");
        }
        return $s;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function setTplFinder(TemplateContentFinderInterface $tplFinder)
    {
        $this->tplFinder = $tplFinder;
        return $this;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function getTplContent($tplId)
    {
        if (false !== $oTpl = $this->getTplFinder()->find($tplId)) {
            return $oTpl->getContent();
        }
        return false;
    }


    /**
     * @return TemplateContentFinderInterface
     */
    private function getTplFinder()
    {
        if (null === $this->tplFinder) {
            $this->tplFinder = new FileSystemTemplateContentFinder();
        }
        return $this->tplFinder;
    }

}
