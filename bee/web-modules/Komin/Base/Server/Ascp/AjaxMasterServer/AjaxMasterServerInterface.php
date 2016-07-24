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
 * AjaxMasterServerInterface
 * @author Lingtalfi
 *
 *
 */
interface AjaxMasterServerInterface
{

    /**
     * @return AjaxServerInterface|false
     */
    public function getServer($serverId);

}
