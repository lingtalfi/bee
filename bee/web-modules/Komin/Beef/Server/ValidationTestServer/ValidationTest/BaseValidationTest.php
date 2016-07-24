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

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;


/**
 * BaseValidationTest
 * @author Lingtalfi
 * 2015-01-07
 *
 */
abstract class BaseValidationTest implements ValidationTestInterface
{
    
    protected function getMandatoryParam($name, array $params)
    {

        if (array_key_exists($name, $params)) {
            return $params[$name];
        }
        $msg = sprintf("Validation test: missing mandatory param: %s", $name);
        SuperLogger::getInst()->log('Komin.Beef.Server.Validation.missingMandatoryParam', $msg);
        throw new ValidationTestException($msg);

    }


    protected function translateTags($msgId, array $errorTags = [], $catalogInfo = null, $tags = null, $pluralNumber = null, $lang = null)
    {
        $r = StazyTranslator::getInst()->translate($msgId, $catalogInfo, $tags, $pluralNumber, $lang);
        $keys = array_keys($errorTags);
        $values = array_values($errorTags);
        array_walk($keys, function (&$v) {
            $v = '[' . $v . ']';
        });
        $r = str_replace($keys, $values, $r);
        return $r;
    }

    protected function translate($msgId, $catalogInfo = null, $tags = null, $pluralNumber = null, $lang = null)
    {
        return StazyTranslator::getInst()->translate($msgId, $catalogInfo, $tags, $pluralNumber, $lang);
    }

}
