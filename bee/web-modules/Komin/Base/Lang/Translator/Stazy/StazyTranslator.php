<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\Translator\Stazy;

use Bee\Component\Lang\Translator\TranslatorInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Traits\Stazy\StazyTrait;


/**
 * StazyTranslator
 * @author Lingtalfi
 */
class StazyTranslator
{

    use StazyTrait;

    /**
     * @return TranslatorInterface
     */
    public static function getInst()
    {
        return self::doGetInst('komin.base.lang.translator.translator');
    }
}
