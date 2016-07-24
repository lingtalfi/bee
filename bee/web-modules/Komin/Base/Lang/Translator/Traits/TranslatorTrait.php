<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\Translator\Traits;

use WebModule\Komin\Base\Lang\Translator\Stazy\StazyTranslator;


/**
 * TranslatorTrait
 * @author Lingtalfi
 * 2015-02-10
 *
 */
trait TranslatorTrait
{

    protected function translate($msgId, $catalogInfo = null, array $tags = null, $pluralNumber = null, $lang = null)
    {
        return StazyTranslator::getInst()->translate($msgId, $catalogInfo, $tags, $pluralNumber, $lang);
    }
}
