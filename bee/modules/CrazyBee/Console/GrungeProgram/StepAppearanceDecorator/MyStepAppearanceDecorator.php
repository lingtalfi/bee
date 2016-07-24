<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Console\GrungeProgram\StepAppearanceDecorator;


/**
 * MyStepAppearanceDecorator
 * @author Lingtalfi
 * 2015-05-20
 *
 */
class MyStepAppearanceDecorator extends StepAppearanceDecorator
{

    public function __construct()
    {
//        $s = str_repeat('-', 15);
//        $s1 = PHP_EOL . 'BEGIN' . $s . PHP_EOL;
//        $s2 = PHP_EOL . 'END' . $s . PHP_EOL;

        $this->setStepAffixes(['', '']);

        $this->setHeadAffixes([PHP_EOL, '']);
        $this->setQuestionAffixes([PHP_EOL, PHP_EOL]);
        $this->setInputAffixes([PHP_EOL, ': ']);
        $this->setTailAffixes([PHP_EOL, '']);

    }

    public static function create()
    {
        return new static();
    }

}
