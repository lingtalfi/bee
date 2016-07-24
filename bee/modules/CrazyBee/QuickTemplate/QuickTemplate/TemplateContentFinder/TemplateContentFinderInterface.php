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

use CrazyBee\QuickTemplate\QuickTemplate\TemplateObject\TemplateObjectInterface;


/**
 * TemplateContentFinderInterface
 * @author Lingtalfi
 * 2015-05-28
 *
 */
interface TemplateContentFinderInterface
{

    /**
     * @param $tplId
     * @return TemplateObjectInterface|false
     */
    public function find($tplId);
}
