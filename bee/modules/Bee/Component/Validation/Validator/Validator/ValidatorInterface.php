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

use Bee\Component\Validation\Validator\ValidatorResult\ValidatorResultInterface;


/**
 * ValidatorInterface
 * @author Lingtalfi
 * 2015-05-07
 *
 */
interface ValidatorInterface
{


    /**
     * @return true|ValidatorResultInterface
     */
    public function validate($value);

    public function setParams(array $params);

    public function setRequirementPhrase($requirementPhrase);

}
