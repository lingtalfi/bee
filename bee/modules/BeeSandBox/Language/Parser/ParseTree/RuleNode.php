<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\ParseTree;


/**
 * RuleNode
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class RuleNode extends ParseTree
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

}
