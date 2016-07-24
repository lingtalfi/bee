<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Notation\File\BabyDash\IndentedLines;

use Bee\Notation\File\IndentedLines\ValueInterpreter\ValueInterpreterInterface;
use Bee\Notation\String\LineParser\ExpressionParser\HybridExpressionParser;
use Bee\Notation\String\LineParser\ExpressionParser\MappingContainerExpressionParser;
use Bee\Notation\String\LineParser\ExpressionParser\QuotedExpressionParser;
use Bee\Notation\String\LineParser\ExpressionParser\SequenceContainerExpressionParser;
use Bee\Notation\String\LineParser\ExpressionParserPile\ExpressionParserPile;
use Bee\Notation\String\LineParser\ExpressionParserPile\ExpressionParserPileInterface;
use Bee\Notation\String\LineParser\NotationParser\NotationParserInterface;
use Bee\Notation\String\LineParser\NotationParser\SingleExpressionNotationParser;


/**
 * BabyDashValueInterpreter
 * @author Lingtalfi
 * 2015-04-17
 *
 */
class BabyDashValueInterpreter implements ValueInterpreterInterface
{

    /**
     * @var NotationParserInterface
     */
    protected $parser;

    /**
     * @var ExpressionParserPileInterface
     */
    protected $pile;
    protected $errors;

    public function __construct()
    {

        $parsers = [
            new QuotedExpressionParser(),
            new HybridExpressionParser(),
        ];
        $this->pile = new ExpressionParserPile($parsers);
        $this->parser = new SingleExpressionNotationParser($this->pile, [
            'commentSymbol' => " #",
        ]);
        $this->errors = [];
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ValueInterpreterInterface
    //------------------------------------------------------------------------------/
    /**
     * @return mixed
     */
    public function getValue($line)
    {
        $this->errors = [];
        if (true === $this->parser->parse($line)) {
            return $this->parser->getValue();
        }
        $this->errors = $this->parser->getErrors();
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function hasError()
    {
        return (!empty($this->errors));
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function resetPile($useMap = false, $useSequence = false)
    {
        if (true === $useSequence) {
            $seq = new SequenceContainerExpressionParser();
            $this->pile->addParser($seq, true);
            $seq->setPile($this->pile);
        }
        if (true === $useMap) {
            $map = new MappingContainerExpressionParser();
            $this->pile->addParser($map, true);
            $map->setPile($this->pile);
        }
        
    }

}
