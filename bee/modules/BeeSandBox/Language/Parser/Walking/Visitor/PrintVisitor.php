<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeeSandBox\Language\Parser\Walking\Visitor;


/**
 * PrintVisitor
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class PrintVisitor implements VecMathVisitorInterface
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS VecMathVisitorInterface
    //------------------------------------------------------------------------------/
    public function visit(VecMathNode $n)
    {
        if($n instanceof AssignNode){
            $n->id->visit($this);
            echo '=';
            $n->value->visit($this);
            echo $this->newLine();
        }
        elseif($n instanceof StatListNode){
            foreach($n->elements as $el){
                $el->visit($this);
            }
        }
        elseif($n instanceof PrintNode){
            echo 'print ';
            $n->value->visit($this);
            echo $this->newLine();
        }
        elseif($n instanceof AddNode){
            $n->left->visit($this);
            echo "+";
            $n->right->visit($this);
        }
        elseif($n instanceof DotProductNode){
            $n->left->visit($this);
            echo ".";
            $n->right->visit($this);
        }
        elseif($n instanceof MultNode){
            $n->left->visit($this);
            echo "*";
            $n->right->visit($this);
        }
        elseif($n instanceof IntNode){
            echo $n->toString();
        }
        elseif($n instanceof VarNode){
            echo $n->toString();
        }
        elseif($n instanceof VectorNode){
            echo '[';
            if (is_array($n->elements)) {
                $sum = count($n->elements);
                for ($i = 0; $i < $sum; $i++) {
                    $child = $n->elements[$i];
                    if ($i > 0) {
                        echo ', ';
                    }
                    $child->visit($this);
                }
            }
            echo ']';
        }
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function newLine()
    {
        if ('cli' === PHP_SAPI) {
            return PHP_EOL;
        }
        return '<br>';
    }
}
