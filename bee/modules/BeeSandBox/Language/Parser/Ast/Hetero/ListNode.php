<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Hetero;


/**
 * ListNode
 * @author Lingtalfi
 * 2015-06-29
 *  A flat tree ==  tree with nil root: (nil child1 child2 ...)
 */
class ListNode extends HeteroAst
{
    private $elements;

    public function __construct($token = null)
    {
        parent::__construct($token);
        $this->elements = [];
    }

    public static function fromElements(array $elements)
    {
        $o = new ListNode();
        $o->elements = $elements;
    }


    public function toStringTree()
    {
        $n = count($this->elements);
        if (null === $this->elements || 0 === $n) {
            return $this->__toString();
        }
        $s = '';
        for ($i = 0; $i < $n; $i++) {
            $t = $this->elements[$i]; // normalized (unnamed) elements
            if ($i > 0) {
                $s .= ' ';
            }
            $s .= $t->toStringTree();
        }
        return $s;
    }


}
