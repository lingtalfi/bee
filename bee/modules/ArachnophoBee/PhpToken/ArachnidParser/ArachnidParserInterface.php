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


/**
 * ArachnidParserInterface
 * @author Lingtalfi
 * 2015-04-12
 *
 */
interface ArachnidParserInterface
{


    /**
     * Executes the steps on the given content,
     * and returns the result.
     *
     * @return string
     */
    public function parse($content);


    /**
     * @param array $steps , array of id => $steps
     */
    public function setSteps(array $steps);

    public function getSteps();

    public function removeStep($id);


}
