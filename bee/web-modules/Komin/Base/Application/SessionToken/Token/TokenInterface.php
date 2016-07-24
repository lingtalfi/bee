<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\SessionToken\Token;


/**
 * TokenInterface
 * @author Lingtalfi
 *
 * Beware that the set and unset methods have to act on the session array directly.
 *
 */
interface TokenInterface
{

    public function set($key, $value);

    public function get($key, $default = null);

    public function all();

    public function has($key);

    public function unsetValue($key);
}
