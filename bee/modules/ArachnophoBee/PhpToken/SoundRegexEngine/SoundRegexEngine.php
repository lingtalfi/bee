<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\SoundRegexEngine;

use ArachnophoBee\PhpToken\SoundRegexEngine\PreParser\PreParserInterface;
use ArachnophoBee\PhpToken\SoundRegexEngine\Tools\SoundRegexTool;
use ArachnophoBee\PhpToken\Tool\TokenTool;


/**
 * SoundRegexEngine
 * @author Lingtalfi
 * 2015-04-07
 *
 */
class SoundRegexEngine implements SoundRegexEngineInterface
{

    protected $combos;

    /**
     * @var PreParserInterface|null
     */
    protected $preparser;


    //
    private $_regex; // cache for replaceCallback
    private $_tokens; // cache for replaceCallback

    public function __construct()
    {
        $this->combos = [];
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS SoundRegexEngineInterface
    //------------------------------------------------------------------------------/
    public function matchAll($pattern, $string, array &$matches = [], $offset = 0)
    {
        $pattern = $this->preparePattern($pattern);
        $tokens = $this->getTokensByContent($string);
        $regex = SoundRegexTool::getSoundRegex($tokens);
        $this->_regex = $regex;
        $this->_tokens = $tokens;
        $flags = PREG_OFFSET_CAPTURE | PREG_SET_ORDER;
        $ret = preg_match_all($pattern, $regex, $matches, $flags, $offset);
        foreach ($matches as $j => $_match) {
            foreach ($_match as $k => $match) {
                $info = $this->getMatchInfo($match, $regex, $tokens);
                $matches[$j][$k][2] = $info[0];
                $matches[$j][$k][3] = $info[1];
            }
        }
        if ($ret) {
            $ret = true;
        }
        else {
            $ret = false;
        }
        return $ret;
    }

    public function match($pattern, $string, array &$matches = [], $offset = 0)
    {

        $pattern = $this->preparePattern($pattern);
        $tokens = $this->getTokensByContent($string);
        $regex = SoundRegexTool::getSoundRegex($tokens);
        $flags = PREG_OFFSET_CAPTURE;
        $ret = preg_match($pattern, $regex, $matches, $flags, $offset);
        foreach ($matches as $k => $match) {
            $info = $this->getMatchInfo($match, $regex, $tokens);
            $matches[$k][2] = $info[0];
            $matches[$k][3] = $info[1];
        }
        if ($ret) {
            $ret = true;
        }
        else {
            $ret = false;
        }
        return $ret;
    }


    public function replaceCallback($pattern, $callback, $string)
    {
        $matches = [];

        if (is_callable($callback)) {


            if (true === $this->matchAll($pattern, $string, $matches, 0)) {

                /**
                 * We first find the positions of the slices to update from the token array.
                 * Each position contains the following entries:
                 *          0: index of the first token of the match to replace
                 *          1: how many tokens the match contains
                 *          2: the replacement string
                 *
                 */
                $slices = [];
                foreach ($matches as $match) {
                    $fullMatch = $match[0];

                    $cbRet = call_user_func($callback, $fullMatch);
                    $offset = $fullMatch[1];
                    $nbTokens = $this->countNbTokens($fullMatch[0]);
                    $sliceIndex = $this->countNbTokens(substr($this->_regex, 0, $offset));


                    $slices[] = [
                        $sliceIndex,
                        $nbTokens,
                        $cbRet,
                    ];

                }


                /**
                 * Now we take the original tokens array and cut/insert our slices into it
                 */
                $tokens = $this->applySlices($slices, 'TMP_REPLACE', $this->_tokens);
                $string = TokenTool::tokensToString($tokens);
            }
        }
        else {
            throw new \InvalidArgumentException("argument callback must be of type callable");
        }
        return $string;
    }


    public function setCombo($comboName, $pattern)
    {
        if (false !== strpos($pattern, 'K_')) {
            throw new \InvalidArgumentException("In this implementation, a combo pattern must not contain the string K_");
        }
        $this->combos[$comboName] = $pattern;
    }

    public function getCombos()
    {
        return $this->combos;
    }

    /**
     * @return false|string
     */
    public function getCombo($comboName)
    {
        if (array_key_exists($comboName, $this->combos)) {
            return $this->combos[$comboName];
        }
        return false;
    }

    public function setPreParser(PreParserInterface $preparser)
    {
        $this->preparser = $preparser;
    }

    /**
     * @return PreParserInterface|null
     */
    public function getPreParser()
    {
        return $this->preparser;
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function preparePattern($pattern)
    {
        $pattern = $this->patternToString($pattern);
        $pattern = preg_replace('!\s+!', ' ', $pattern);

        // wrap all tokens
        $pattern = preg_replace('!\bT_([a-zA-Z_]+)\b!', '(?:K_$1)', $pattern);
        $pattern = preg_replace('!\s+!', '', $pattern);
        $pattern = '!' . $pattern . '!';
        return $pattern;
    }

    private function patternToString($pattern)
    {
        if (is_array($pattern)) {
            $pattern = implode(' ', $pattern);
        }
        return $pattern;
    }

    private function getTokensByContent($string)
    {
        $tokens = token_get_all($string);
        if ($this->preparser instanceof PreParserInterface) {
            $tokens = $this->preparser->preparse($tokens);
        }
        return $tokens;
    }

    private function getMatchInfo(array $match, $regex, array $tokens)
    {
        $off = $match[1];
        $begin = substr($regex, 0, $off);
        $nbTokens = $this->countNbTokens($begin);
        $start = $nbTokens;
        $nbTokens2 = $this->countNbTokens($match[0]);
        $length = $nbTokens2;
        $subTokens = array_slice($tokens, $start, $length);
        return [TokenTool::tokensToString($subTokens), $subTokens];
    }

    private function countNbTokens($string)
    {
        return substr_count($string, 'K_') + substr_count($string, 'X_');
    }


    protected function applySlices(array $slices, $tokenName, array $tokens)
    {
        $ret = $tokens;
        foreach ($slices as $slice) {
            $anchorPos = $slice[0];

            $original = $ret[$anchorPos];
            $ret[$anchorPos] = [
                $tokenName,
                $slice[2],
                $original[2], // line number
            ];

            $anchorPos++;
            $length = $slice[1] - 1;

            for ($i = 0; $i < $length; $i++) {
                unset($ret[$anchorPos]);
                $anchorPos++;
            }
        }
        return array_merge($ret);
    }

}
