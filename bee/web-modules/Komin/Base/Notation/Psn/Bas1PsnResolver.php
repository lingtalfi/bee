<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Notation\Psn;

use Bee\Notation\String\Psn\PsnResolver;


/**
 * Bas1PsnResolver.
 * @author Lingtalfi
 *
 */
class Bas1PsnResolver extends PsnResolver
{



    public function __construct($applicationRootDir, array $userSymbols = array())
    {
        $root = $applicationRootDir;
        $this->symbols['[root]'] = $root;
        $this->symbols['[app]'] = $root . '/app';
        $this->symbols['[cache]'] = $root . '/app/cache';
        $this->symbols['[config]'] = $root . '/app/config';
        $this->symbols['[log]'] = $root . '/app/log';
        $this->symbols['[lib]'] = $root . '/lib';
        $this->symbols['[packages]'] = $root . '/packages';
        $this->symbols['[private]'] = $root . '/private';
        $this->symbols['[src]'] = $root . '/src';
        $this->symbols['[web]'] = $root . '/web';

        parent::__construct($userSymbols);
    }

}
