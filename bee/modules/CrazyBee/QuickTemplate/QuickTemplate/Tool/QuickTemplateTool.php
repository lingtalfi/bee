<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\QuickTemplate\QuickTemplate\Tool;


/**
 * QuickTemplateTool
 * @author Lingtalfi
 * 2015-05-28
 *
 */
class QuickTemplateTool
{

    public static function getTemplateIdInfo($tplId)
    {
        $group = 'default';
        $p = explode(':', $tplId, 2);
        if (2 === count($p)) {
            $group = $p[1];
        }
        return [$p[0], $group];
    }
}
