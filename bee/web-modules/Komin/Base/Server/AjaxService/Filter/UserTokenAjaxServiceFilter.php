<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\AjaxService\Filter;

use WebModule\Komin\User\UserToken\Stazy\StazyUserToken;
use WebModule\Komin\User\UserToken\UserTokenTool;


/**
 * UserTokenAjaxServiceFilter
 * @author Lingtalfi
 *
 *
 */
class UserTokenAjaxServiceFilter implements AjaxServiceFilterInterface
{

    protected $key;

    public function __construct($key = null)
    {
        if (null === $key) {
            $key = '_badges';
        }
        $this->key = $key;
    }


    public function filter(array $node)
    {
        if (array_key_exists($this->key, $node)) {
            $badges = $node[$this->key];
            if (false === UserTokenTool::hasBadges($badges)) {
                throw new AjaxFilterException("You are not allowed to perform this action");
            }
        }
    }
}
