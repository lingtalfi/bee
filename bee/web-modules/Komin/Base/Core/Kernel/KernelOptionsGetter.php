<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Core\Kernel;


/**
 * KernelOptionsGetter
 * @author Lingtalfi
 * 2014-10-20
 * 
 * 
 *
 */
class KernelOptionsGetter
{


    private static $options = [];

    public static function init(array $options)
    {
        self::$options = $options;
    }

    public static function get($key, $defaultValue = null)
    {
        if (array_key_exists($key, self::$options)) {
            return self::$options[$key];
        }
        return $defaultValue;
    }

}
