<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\PhpToken\ArachnidParser;

use ArachnophoBee\PhpToken\ArachnidParser\ArachnidParserStep\ApplyAlphabetArachnidParserStep;
use ArachnophoBee\PhpToken\ArachnidParser\ArachnidParserStep\UnconcatenateArachnidParserStep;
use Bee\Bat\FileSystemTool;
use Bee\Bat\FileTool;
use Bee\Bat\SanitizerTool;


/**
 * FirstWpInjectArachnidParser
 * @author Lingtalfi
 * 2015-04-12
 *
 * This parser is the reason why the arachnid parser exists in the first place.
 * One of my wordPress was hacked and the malicious guy injected some php script with
 * most values replaced by array references.
 *
 *
 * So this class aims toward converting this "mysterious" php script into a readable one,
 * so that we can see what's going on without too much manual efforts.
 *
 *
 * In particular, this class contains the steps I needed to demystify my script.
 * Each malicious code might be different though.
 * Therefore, this is more a demo class, or a quick start class rather than a class you
 * might be able to reuse.
 *
 *
 *
 */
class FirstWpInjectArachnidParser extends ArachnidParser
{
    public function __construct()
    {
        parent::__construct();
        $this->setSteps([
//            'applyFirstAlphabet' => new ApplyAlphabetArachnidParserStep(),
//            'unconcatenate' => new UnconcatenateArachnidParserStep(),
            'applySecondAlphabet' => new ApplyAlphabetArachnidParserStep(),
        ]);
    }


}
