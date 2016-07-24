<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Server\Ascp\AjaxServer;

use Bee\Bat\HttpTool;


/**
 * AjaxServer
 * @author Lingtalfi
 *
 *
 */
abstract class AjaxServer implements AjaxServerInterface
{

    protected $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    /**
     * @return mixed|false, false on failure, in which case errors should be set.
     *                      mixed in case of success.
     */
    abstract protected function doExecute($serviceId, array $params = []);

    /**
     * @return false|mixed, false on failure,
     *                          in which case errors should be available
     */
    public function execute($serviceId, array $params = [])
    {
        $params = $this->prepareParams($params);
        return $this->doExecute($serviceId, $params);
    }

    /**
     * @return array
     */

    public function getErrors()
    {
        return $this->errors;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function prepareParams(array $params)
    {
        return HttpTool::resolveLiteralValue($params);
    }

    protected function error($msg)
    {
        $msg = 'AjaxServer: ' . $msg;
        $this->errors[] = $msg;
        return false;
    }


}
