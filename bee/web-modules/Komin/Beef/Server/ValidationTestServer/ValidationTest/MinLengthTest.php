<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\ValidationTestServer\ValidationTest;

/**
 * MinLengthTest
 * @author Lingtalfi
 * 2015-01-07
 *
 */
class MinLengthTest extends BaseValidationTest
{

    public function execute($value, array $params)
    {
        $ret = true;
        if (is_string($value)) {
            $minLength = (int)$this->getMandatoryParam('min', $params);
            $curLength = strlen($value);
            if ($curLength < $minLength) {
                $ret = $this->translateTags("Please type more than [min] chars ([currentLength] given)", [
                    'min' => $minLength,
                    'currentLength' => $curLength,
                ], $this);
            }
        }
        else {
            $ret = $this->translate("The given value is not a string", null, $this);
        }
        return $ret;
    }


}
