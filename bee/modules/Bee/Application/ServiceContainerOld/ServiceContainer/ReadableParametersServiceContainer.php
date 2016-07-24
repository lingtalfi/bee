<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Application\ServiceContainer\ServiceContainer;

use Bee\Application\ParameterBag\BdotParameterBag;
use Bee\Application\ParameterBag\ParameterBagInterface;
use Bee\Application\ServiceContainer\ServiceContainer\Exception\ServiceContainerException;
use Bee\Bat\BdotTool;


/**
 * ReadableParametersServiceContainer
 * @author Lingtalfi
 * 2015-03-08
 *
 *
 */
class ReadableParametersServiceContainer extends ServiceContainer implements ReadableParametersServiceContainerInterface
{

    private $paramBag;

    public function __construct(ParameterBagInterface $paramBag = null)
    {
        parent::__construct();
        if (null === $paramBag) {
            $paramBag = BdotParameterBag::create();
        }
        $this->paramBag = $paramBag;
    }


    /**
     * This method is intended for plugin authors,
     * so that they can access the parameters of their own plugins to provide
     * working services.
     *
     * @return mixed
     * @throws \Exception
     */
    public function getParameter($name)
    {
        $ret = $this->paramBag->get($name, 42);
        if (42 === $ret && false === $this->paramBag->has($name)) {
            throw new ServiceContainerException(sprintf("Parameter not found: %s", $name));
        }
        return $ret;
    }
}
