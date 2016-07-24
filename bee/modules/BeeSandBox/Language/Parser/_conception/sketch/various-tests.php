<?php
#!/usr/bin/env php


//require_once 'alveolus/bee/boot/bam1.php';

use BeeSandBox\Language\Parser\Ast\Hetero\AddNode as HeteroAddNode;
use BeeSandBox\Language\Parser\Ast\Hetero\HeteroToken;
use BeeSandBox\Language\Parser\Ast\Hetero\IntNode as HeteroIntNode;
use BeeSandBox\Language\Parser\Ast\Homogeneous\HomogeneousAst;
use BeeSandBox\Language\Parser\Ast\Homogeneous\HomogeneousToken;
use BeeSandBox\Language\Parser\Ast\Normalized\AddNode;
use BeeSandBox\Language\Parser\Ast\Normalized\IntNode;
use BeeSandBox\Language\Parser\Ast\Normalized\NormalizedToken;
use BeeSandBox\Language\Parser\Lexer\BacktrackLexer;
use BeeSandBox\Language\Parser\Lexer\LexerInterface;
use BeeSandBox\Language\Parser\Lexer\ListLexer;
use BeeSandBox\Language\Parser\Lexer\LookaheadLexer;
use BeeSandBox\Language\Parser\Lexer\Token;
use BeeSandBox\Language\Parser\Lexer\Tool\LexerDevTool;
use BeeSandBox\Language\Parser\Parser\BacktrackingListParser;
use BeeSandBox\Language\Parser\Parser\LL1ListParser;
use BeeSandBox\Language\Parser\Parser\LLkListParser;
use BeeSandBox\Language\Parser\Parser\MemoizingBacktrackingListParser;
use BeeSandBox\Language\Parser\Parser\MyLLkListParserWithParseTree;
use BeeSandBox\Language\Parser\ParseTree\Tool\DebugParseTreeTool;
use BeeSandBox\Language\Parser\Walking\Visitor\AddNode as VAddNode;
use BeeSandBox\Language\Parser\Walking\Visitor\AssignNode as VAssignNode;
use BeeSandBox\Language\Parser\Walking\Visitor\IndependentPrintVisitor;
use BeeSandBox\Language\Parser\Walking\Visitor\IntNode as VIntNode;
use BeeSandBox\Language\Parser\Walking\Visitor\MultNode as VMultNode;
use BeeSandBox\Language\Parser\Walking\Visitor\PrintNode;
use BeeSandBox\Language\Parser\Walking\Visitor\PrintVisitor;
use BeeSandBox\Language\Parser\Walking\Visitor\StatListNode;
use BeeSandBox\Language\Parser\Walking\Visitor\Token as VToken;
use BeeSandBox\Language\Parser\Walking\Visitor\VarNode as VVarNode;
use BeeSandBox\Language\Parser\Walking\Visitor\VectorNode as VVectorNode;
use Bware\SymphoBee\Routing\Sombrero\Router\StaticSombreroRouter;

require_once 'alveolus/bee/boot/autoload.php';
require_once 'vendor/autoload.php';

ini_set('error_reporting', -1);
ini_set('display_errors', 1);


$test = 9;


//------------------------------------------------------------------------------/
// READER
//------------------------------------------------------------------------------/
if (1 === $test) { // LL1 parser
    $input = 'abc, déf, [ou[c]h]';
    $input = 'abc, déf, [ouc, [], h]';
    $input = '[abc, déf, [gg], [ouch, do, [aoo]]  ] # really awesome, I love it';
    $lexer = new ListLexer($input);
    LexerDevTool::dumpTokens($lexer, ListLexer::$tokenNames);
    $lexer->rewind();

    $parser = new LL1ListParser($lexer);
    $parser->setOnMatch(function ($x, LexerInterface $input, Token $token) {
        echo $input->getTokenName($token->type) . '-';
    });
    $parser->elementsList();
}
elseif (2 === $test) { // LLK parser
    $input = '[a,b=c,[d,e]]';
    $lexer = new LookaheadLexer($input);
    LexerDevTool::dumpTokens($lexer, LookaheadLexer::$tokenNames);
    $lexer->rewind();

    $parser = new LLkListParser($lexer, 2);
    $parser->setOnMatch(function ($x, LexerInterface $input, Token $token) {
        echo $input->getTokenName($token->type) . '-';
    });
    $parser->elementsList();
}
elseif (3 === $test) { // Backtracking parser
    $input = '[a,b]=[c,d]';
    $input = '[a,b=c,[d,e]]';
    $lexer = new BacktrackLexer($input);
    LexerDevTool::dumpTokens($lexer, BacktrackLexer::$tokenNames);
    $lexer->rewind();

    $parser = new BacktrackingListParser($lexer);
    $parser->setOnMatch(function ($x, LexerInterface $input, Token $token) {
        echo $input->getTokenName($token->type) . '-';
    });
    $parser->stat();
}
elseif (4 === $test) { // Backtracking parser with memoization
    $input = '[a,b=c,[d,e]]';
    $input = '[a,b]=[c,d]';
    $lexer = new BacktrackLexer($input);
    LexerDevTool::dumpTokens($lexer, BacktrackLexer::$tokenNames);
    $lexer->rewind();

    $parser = new MemoizingBacktrackingListParser($lexer);
    $parser->setOnMatch(function ($x, LexerInterface $input, Token $token) {
        echo $input->getTokenName($token->type) . '-';
    });
    $parser->stat();
}
elseif (5 === $test) {
    $input = '[a,bo=c,[d,e]]';
    $lexer = new LookaheadLexer($input);
    LexerDevTool::dumpTokens($lexer, LookaheadLexer::$tokenNames);
    $lexer->rewind();

    $parser = new MyLLkListParserWithParseTree($lexer, 2);
    $parser->setOnMatch(function ($x, LexerInterface $input, Token $token) {
        echo $input->getTokenName($token->type) . '-';
    });
    $parser->elementsList();
    echo '<hr>';
    echo DebugParseTreeTool::renderParseTree($parser->getParseTree(), 3);
}
//------------------------------------------------------------------------------/
// AST
//------------------------------------------------------------------------------/
elseif (6 === $test) {

    $plus = new HomogeneousToken(HomogeneousToken::PLUS, "+");
    $one = new HomogeneousToken(HomogeneousToken::INT, "1");
    $two = new HomogeneousToken(HomogeneousToken::INT, "2");
    $root = new HomogeneousAst($plus);
    $root->addChild(new HomogeneousAst($one));
    $root->addChild(new HomogeneousAst($two));
    echo "1+2 tree: " . $root->toStringTree();
    echo '<br>';

    $list = new HomogeneousAst();
    $list->addChild(new HomogeneousAst($one));
    $list->addChild(new HomogeneousAst($two));
    echo "1+2 list: " . $list->toStringTree();


}
elseif (7 === $test) {

    $plus = new NormalizedToken(NormalizedToken::PLUS, "+");
    $one = new NormalizedToken(NormalizedToken::INT, "1");
    $two = new NormalizedToken(NormalizedToken::INT, "2");
    $root = AddNode::constructAddNode(new IntNode($one), $plus, new IntNode($two));
    echo $root->toStringTree();
}
elseif (8 === $test) {

    $plus = new HeteroToken(HeteroToken::PLUS, "+");
    $one = new HeteroToken(HeteroToken::INT, "1");
    $two = new HeteroToken(HeteroToken::INT, "2");
    $root = HeteroAddNode::constructAddNode(new HeteroIntNode($one), $plus, new HeteroIntNode($two));
    echo $root->toStringTree();
}
//------------------------------------------------------------------------------/
// WALKING
//------------------------------------------------------------------------------/
elseif (9 === $test) { // external visitor

    /**
     * @param $i
     * @return VIntNode
     */
    function I($i)
    {
        return new VIntNode(new VToken(VToken::INT, $i));
    }

    // x = 3+4
    $stats = [];
    $a = VAddNode::constructAddNode(I(3), new VToken(VToken::PLUS), I(4));
    $x = new VVarNode(new VToken(VToken::ID, 'x'));
    $assign = VAssignNode::constructAssignNode($x, new VToken(VToken::ASSIGN, '='), $a);
    $stats[] = $assign;


    // print x * [2, 3, 4]
    $mult = new VToken(VToken::MULT, '*');
    $elements = [];
    $elements[] = I(2);
    $elements[] = I(3);
    $elements[] = I(4);

    $v = new VVectorNode(new VToken(VToken::VEC), $elements);
    $xref = new VVarNode(new VToken(VToken::ID, 'x'));
    $pv = VMultNode::constructMultNode($xref, $mult, $v);
    $p = new PrintNode(new VToken(VToken::_PRINT, 'print'), $pv);
    $stats[] = $p;
    $statList = new StatListNode(null, $stats);


    // Create visitor and then call visit on root node (statlist)
    $visitor = new PrintVisitor();
    $statList->visit($visitor);

    echo '<hr>';

    // Create visitor and then visit root node (statlist)
    $iVisitor = new IndependentPrintVisitor();
    $iVisitor->prints($statList);

}
