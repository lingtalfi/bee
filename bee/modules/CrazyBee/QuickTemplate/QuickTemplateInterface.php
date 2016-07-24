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


/**
 * QuickTemplateInterface
 * @author Lingtalfi
 * 2015-05-27
 *
 */
interface QuickTemplateInterface
{



    /**
     * @param $tplId , is defined by concrete implementations.
     * @return string
     */
    public function render($tplId, array $tags = []);

}
