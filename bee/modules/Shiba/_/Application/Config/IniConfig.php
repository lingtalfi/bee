<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shiba\Application\Config;


/**
 * IniConfig
 * @author Lingtalfi
 * 2015-04-03
 *
 */
class IniConfig implements ConfigInterface
{

    protected $parameters;

    public function __construct($path)
    {
        if (file_exists($path)) {
            $this->parameters = parse_ini_file($path);
        }
        else {
            throw new \RuntimeException(sprintf("File not found: %s", $path));
        }
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS ApplicationConfigInterface
    //------------------------------------------------------------------------------/
    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParameter($key, $ret = null)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
        return $ret;
    }

}
