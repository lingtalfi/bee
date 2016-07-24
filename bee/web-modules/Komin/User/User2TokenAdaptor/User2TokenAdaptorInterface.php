<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\User2TokenAdaptor;


/**
 * User2TokenAdaptorInterface
 * @author Lingtalfi
 *
 *
 */
interface User2TokenAdaptorInterface
{

    /**
     * @return array:
     *      - array     userData
     *      - array     badges
     */
    public function getUserData(array $user);
}
