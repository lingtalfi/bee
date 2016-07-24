<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\Ascp\AjaxMasterServer;

use WebModule\Komin\Base\Server\Ascp\AjaxServer\AjaxServerInterface;


/**
 * AjaxMasterServer
 * @author Lingtalfi
 *
 *
 */
class AjaxMasterServer implements AjaxMasterServerInterface
{

    protected $ajaxServices;


    public function __construct(array $servers = [])
    {
        $this->ajaxServers = $servers;
    }

    /**
     * @return AjaxServerInterface|false
     */
    public function getServer($serverId)
    {
        if (array_key_exists($serverId, $this->ajaxServers)) {
            return $this->ajaxServers[$serverId];
        }
        return false;
    }
}
