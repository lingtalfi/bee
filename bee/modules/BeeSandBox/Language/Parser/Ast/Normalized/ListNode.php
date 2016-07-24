<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Ast\Normalized;


/**
 * ListNode
 * @author Lingtalfi
 * 2015-06-29
 *  A flat tree ==  tree with nil root: (nil child1 child2 ...)
 */
class ListNode extends ExprNode
{
    public function __construct($token = null, $elements = null)
    {
        parent::__construct($token);
        $this->evalType = self::tVECTOR;
        if (is_array($elements)) {
            foreach ($elements as $el) {
                $this->addChild($el);
            }
        }
    }


}
