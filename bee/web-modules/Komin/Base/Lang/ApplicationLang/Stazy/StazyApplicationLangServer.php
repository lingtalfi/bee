<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Lang\ApplicationLang\Stazy;

use Bee\Component\Lang\Translator\TranslatorInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;
use WebModule\Komin\Base\Lang\ApplicationLang\ApplicationLangServerInterface;


/**
 * StazyApplicationLangServer
 * @author Lingtalfi
 */
class StazyApplicationLangServer
{


    private static $inst;


    private function __construct()
    {

    }


    /**
     * @return ApplicationLangServerInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.lang.applicationLangServer');
        }
        return self::$inst;
    }
}
