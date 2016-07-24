<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot\ComparatorOld\AnalysisStepFormatter;


/**
 * HtmlAnalysisStepFormatter
 * @author Lingtalfi
 * 2015-05-01
 *
 */
class HtmlAnalysisStepFormatter implements AnalysisStepFormatterInterface
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS AnalysisStepFormatterInterface
    //------------------------------------------------------------------------------/
    public function format($msg, $color = "black")
    {
        return '<span style="color: ' . $color . '">' . $msg . '</span>';
    }

}
