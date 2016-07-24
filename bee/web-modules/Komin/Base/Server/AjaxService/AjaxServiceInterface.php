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


/**
 * AjaxServiceInterface
 * @author Lingtalfi
 *
 *
 */
interface AjaxServiceInterface
{

    /**
     * @return false|mixed, false on failure,
     *                          in which case errors should be available
     */
    public function execute($id, array $params = []);

    /**
     * @return array
     */
    public function getErrors();
}
