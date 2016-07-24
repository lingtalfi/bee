<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Server\Ascp\ModeHandler;


/**
 * ModeHandlerInterface
 * @author Lingtalfi
 * 2015-02-05
 *
 */
interface ModeHandlerInterface
{
    /**
     * @return false|mixed, false in case of failure
     */
    public function execute($crudId, array $params);
    

    public function getUserErrors();
}
