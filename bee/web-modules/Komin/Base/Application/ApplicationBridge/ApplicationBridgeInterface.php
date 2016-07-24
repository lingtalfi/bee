<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\ApplicationBridge;


/**
 * ApplicationBridgeInterface
 * @author Lingtalfi
 * 2015-02-16
 *
 */
interface ApplicationBridgeInterface
{
    /**
     * @return mixed|false in case of failure
     */
    public function execute($declarationIdentifier, array $params = []);
}
