<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Repository\RepositoryHandler;



/**
 * RepositoryHandlerInterface
 * @author Lingtalfi
 * 2015-02-15
 * 
 * This is a repository server helper that works for one type of resource (for instance plugin, or package).
 * 
 */
interface RepositoryHandlerInterface {

    /**
     * @return array
     */
    public function getList();
}
