<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\AjaxService;


use WebModule\Komin\Base\Server\AjaxService\Filter\UserTokenAjaxServiceFilter;


/**
 * MeeAjaxService
 * @author Lingtalfi
 *
 *
 */
abstract class MeeAjaxService extends AjaxService
{
    public function __construct(array $nodes, array $filters = [], array $params = [])
    {
        $params = array_replace([
            'badgesKey' => null,
        ], $params);
        $filters[] = new UserTokenAjaxServiceFilter($params['badgesKey']);
        parent::__construct($nodes, $filters);
    }
}
