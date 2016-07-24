<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\FileSystem\Finder\Filter;

use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Filter\Tool\TypeTool;


/**
 * TypeAndBaseNameFinderFilter
 * @author Lingtalfi
 * 2015-05-06
 *
 */
class TypeAndBaseNameFinderFilter extends AbstractPatternFinderFilter
{

    private $types;


    public function __construct($types, $pattern, $isRegex = false, $accept = true, $onAcceptedStopRecursion = false)
    {
        parent::__construct($pattern, $isRegex, $accept);
        $this->types = $types;
    }


    protected function getMethodName()
    {
        return "getBaseName";
    }

    protected function filterFile(FinderFileInfo $f)
    {
        if (TypeTool::typeMatch($this->types, $f)) {
            return parent::filterFile($f);
        }
        return false;
    }

}
