<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\AjaxService\AjaxServiceManager;
use WebModule\Komin\Base\Server\AjaxService\AjaxServiceInterface;


/**
 * AjaxServiceManager
 * @author Lingtalfi
 *
 *
 */
class AjaxServiceManager implements AjaxServiceManagerInterface
{

    protected $ajaxServices;


    public function __construct(array $services = [])
    {
        $this->ajaxServices = $services;
    }

    /**
     * @return AjaxServiceInterface|false
     */
    public function getAjaxService($id)
    {
        if (array_key_exists($id, $this->ajaxServices)) {
            return $this->ajaxServices[$id];
        }
        return false;
    }
}
