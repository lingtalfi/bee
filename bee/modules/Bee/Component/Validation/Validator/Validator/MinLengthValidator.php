<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Validation\Validator\Validator;

use Bee\Component\Validation\Validator\ValidatorTest\MinLengthValidatorTest;
use Bee\Component\Validation\Validator\ValidatorTest\ValidatorTestInterface;


/**
 * MinLengthValidator
 * @author Lingtalfi
 * 2015-05-07
 *
 */
class MinLengthValidator extends BaseValidator
{


    /**
     * @return ValidatorTestInterface
     */
    protected function getValidatorTest()
    {
        return new MinLengthValidatorTest();
    }

    protected function injectAdditionalTags(array &$tags, $value)
    {
        $tags['currentLength'] = strlen($value);
    }

    protected function getDefaultRequirementPhrase()
    {
        return "The text must contain at least {minLength} chars, only {currentLength} given";
    }


}
