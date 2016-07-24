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
 * IndependentPrintVisitor
 * @author Lingtalfi
 * 2015-06-29
 *
 */
class IndependentPrintVisitor
{

    public function prints($n)
    {

        if ($n instanceof VecMathNode) {
            switch ($n->token->type) {
                case Token::ID:
                    $this->printVarNode($n);
                    break;
                case Token::ASSIGN:
                    $this->printAssignNode($n);
                    break;
                case Token::_PRINT:
                    $this->printPrintNode($n);
                    break;
                case Token::PLUS:
                    $this->printAddNode($n);
                    break;
                case Token::MULT:
                    $this->printMultNode($n);
                    break;
                case Token::DOT:
                    $this->printDotProductNode($n);
                    break;
                case Token::INT:
                    $this->printIntNode($n);
                    break;
                case Token::VEC:
                    $this->printVectorNode($n);
                    break;
                case Token::STAT_LIST:
                    $this->printStatListNode($n);
                    break;
                default:
                    // catch unhandled node types
                    throw new \InvalidArgumentException("Node " . get_class($n) . " not handled");
                    break;
            }
        }
    }


    public function printStatListNode(StatListNode $n)
    {
        foreach ($n->elements as $el) {
            $this->prints($el);
        }
    }

    public function printAssignNode(AssignNode $n)
    {
        $this->prints($n->id);
        echo '=';
        $this->prints($n->value);
        echo $this->newLine();
    }

    public function printPrintNode(PrintNode $n)
    {
        echo 'print ';
        $this->prints($n->value);
        echo $this->newLine();
    }


    public function printAddNode(AddNode $n)
    {
        $this->prints($n->left);
        echo '+';
        $this->prints($n->right);
    }

    public function printDotProductNode(DotProductNode $n)
    {
        $this->prints($n->left);
        echo '.';
        $this->prints($n->right);
    }


    public function printMultNode(MultNode $n)
    {
        $this->prints($n->left);
        echo '*';
        $this->prints($n->right);
    }

    public function printIntNode(IntNode $n)
    {
        echo $n->toString();
    }

    public function printVarNode(VarNode $n)
    {
        echo $n->toString();
    }

    public function printVectorNode(VectorNode $n)
    {
        echo '[';
        if (is_array($n->elements)) {
            $sum = count($n->elements);
            for ($i = 0; $i < $sum; $i++) {
                $child = $n->elements[$i];
                if ($i > 0) {
                    echo ', ';
                }
                $this->prints($child);
            }
        }
        echo ']';
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
