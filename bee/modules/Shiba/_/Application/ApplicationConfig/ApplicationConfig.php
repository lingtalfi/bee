<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shiba\Application\ApplicationConfig;

use Shiba\Application\Config\ConfigInterface;


/**
 * ApplicationConfig
 * @author Lingtalfi
 * 2015-04-03
 *
 */
class ApplicationConfig
{


    private static $inst;

    public static function setConfig(ConfigInterface $config)
    {
        self::$inst = $config;
    }


    public static function getParameters()
    {
        return self::getInst()->getParameters();
    }

    public static function getParameter($key, $ret = null)
    {
        return self::getInst()->getParameter($key, $ret);
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return ConfigInterface
     */
    protected static function getInst()
    {
        if (null !== self::$inst) {
            return self::$inst;
        }
        throw new \LogicException("You must call the setConfig method");
    }

}
