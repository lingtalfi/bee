<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Repository\Server\Ascp;

use Bee\Component\Log\SuperLogger\SuperLogger;
use WebModule\Komin\Base\Server\Ascp\AjaxServer\AjaxServer;
use WebModule\Komin\Repository\RepositoryHandler\RepositoryHandlerInterface;


/**
 * RepositoryServer
 * @author Lingtalfi
 * 2015-02-15
 *
 */
class RepositoryServer extends AjaxServer
{


    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            /**
             * array of $type => RepositoryHandlerInterface
             */
            'handlers' => [],
        ], $options);
    }


    /**
     * @return mixed|false, false on failure, in which case errors should be set.
     *                      mixed in case of success.
     */
    public function doExecute($serviceId, array $params = [])
    {
        $ret = false;
        switch ($serviceId) {
            case 'getList':
                if (false !== $handler = $this->getHandler($params)) {
                    return $handler->getList();
                }
                break;
            default:
                $this->log("serviceNotFound", sprintf("Service not found: %s", $serviceId));
                break;
        }
        return $ret;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function error($msg)
    {
        $this->errors[] = $msg;
        return false;
    }

    /**
     * @return RepositoryHandlerInterface
     */
    protected function getHandler(array $params)
    {
        if (array_key_exists('type', $params)) {
            $type = $params['type'];
            $handlers = $this->options['handlers'];
            if (array_key_exists($type, $handlers)) {
                return $handlers[$type];
            }
            else {
                $this->error(sprintf("unknown resource type: %s", $type));
            }
        }
        else {
            $this->error("missing param type");
        }
        return false;
    }

    protected function log($id, $msg)
    {
        SuperLogger::getInst()->log('Komin.Repository.Server.' . $id, $msg);
        $this->error("An error occurred, check the application logs");
        return false;
    }
}
