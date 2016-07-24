<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CrazyBee\Notation\NotationResolver\NotationFinder;

use Bee\Application\ServiceContainer\ServiceContainer\ServiceContainerInterface;
use Bee\Bat\ArrayTool;
use Bee\Notation\String\StringParser\ExpressionDiscoverer\Miscellaneous\FunctionExpressionDiscoverer;


/**
 * AeroBeeCallableNotationFinder
 * @author Lingtalfi
 * 2015-05-17
 *
 * This finder works with the aeroBee notation.
 * This class accepts multiple containers.
 * One of them has to be named _default.
 * The _default container is the one used when the user calls either:
 *
 *      - s
 *      - service
 *      - doesn't specify the container name
 *
 *
 */
class AeroBeeCallableNotationFinder extends CallableNotationFinder
{

    private $containers;
    private $pattern;
    /**
     * mixed callable (paramValue)
     *              This callable allows us to set custom parameters notation.
     *              It takes the original parameter as its argument, and should return the new parameter value.
     *
     */
    private $parametersAdaptor;
    /**
     *
     * mixed    callable ($beforeOperator, $operator, $method, array $params, &$wasSpecial = false)
     *
     *                  This callable gives us the opportunity to override the default processing of
     *                  the array returned by the FunctionExpressionDiscoverer.
     *
     *                  If we do so, we return the processed value AND we set the $wasSpecial flag to true
     *                  to indicate that the value was actually overridden (otherwise, our returned value will
     *                  be ignored).
     */
    private $specialFunctionProcessor;

    public function __construct()
    {
        parent::__construct();
        $this->containers = [];
        $this->pattern = '!
^@
 ([a-zA-Z0-9:_\\\.]+?)
 (
    ::
    |
    ->
    |
    :
 )
 ([a-zA-Z0-9_]+)
 \s*
 \(

!x';

        $this->setDiscoverer(FunctionExpressionDiscoverer::create()
                ->setPattern($this->pattern)
                ->setNotSignificantSymbols(["\n", " ", "\t"])
        );
    }

    public function getValue()
    {
        $v = parent::getValue();
        $v = $this->interpretDiscovererArray($v);
        return $v;
    }

    public function setStartSymbol($s)
    {
        parent::setStartSymbol($s);
        $this->getDiscoverer()->setPattern(str_replace('@', $s, $this->pattern));
        return $this;
    }

    public function setContainer($name, ServiceContainerInterface $c)
    {
        $this->containers[$name] = $c;
        return $this;
    }

    public function setContainers(array $containers)
    {
        $this->containers = $containers;
        return $this;
    }

    public function setParametersAdaptor($parametersAdaptor)
    {
        $this->parametersAdaptor = $parametersAdaptor;
        return $this;
    }

    public function setSpecialFunctionProcessor($callback)
    {
        $this->specialFunctionProcessor = $callback;
        return $this;
    }




    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function interpretDiscovererArray(array $match)
    {

        $ret = null;
        if (
            array_key_exists('_func', $match) &&
            array_key_exists('_params', $match)
        ) {
            $func = $match['_func'];
            $params = $match['_params'];
            if ($params) {
                $params = $this->notationResolver->parseValue($params);
                $params = $this->interpretParameters($params);
            }

            $p1 = $func[1];
            $op = $func[2];
            $method = $func[3];


            $wasSpecial = false;
            if (is_callable($this->specialFunctionProcessor)) {
                $val = call_user_func_array($this->specialFunctionProcessor, [$p1, $op, $method, $params, &$wasSpecial]);
            }

            if (true === $wasSpecial) {
                $ret = $val;
            }
            else {
                if ('::' === $op) {
                    $className = $p1;
                    $ret = call_user_func_array([$className, $method], $params);
                }
                elseif (':' === $op) {
                    // php
                    if (function_exists($method)) {
                        $ret = call_user_func_array($method, $params);

                    }
                    else {
                        throw new \RuntimeException("Unknown php function: $method");
                    }
                }
                else {
                    $p = explode(':', $p1, 2);
                    if (2 === count($p)) {
                        list($containerName, $address) = $p;
                        if ('s' === $containerName || 'service' === $containerName) {
                            $containerName = '_default';
                        }
                    }
                    else {
                        $address = $p1;
                        $containerName = '_default';
                    }

                    if (array_key_exists($containerName, $this->containers)) {
                        $container = $this->containers[$containerName];
                        /**
                         * @var ServiceContainerInterface $container
                         */
                        $newInstance = false;
                        if ('+' === substr($address, -1)) {
                            $newInstance = true;
                            $address = substr($address, 0, -1);
                        }
                        $service = $container->getService($address, null, $newInstance);
                        $ret = call_user_func_array([$service, $method], $params);
                    }
                    else {
                        throw new \RuntimeException("Container not found: $containerName");
                    }
                }
            }


            if (is_string($ret) || is_array($ret)) {
                $ret = $this->notationResolver->parseValue($ret);
            }
        }
        else {
            throw new \RuntimeException("match argument must contain both properties: _func and _params");
        }
        return $ret;
    }


    private function interpretParameters(array $params)
    {
        foreach ($params as $k => $v) {
            if (is_array($v) && ArrayTool::hasKeys($v, ['_func', '_params'])) {
                $params[$k] = $this->interpretDiscovererArray($v);
            }
            if (is_callable($this->parametersAdaptor)) {
                $params[$k] = call_user_func($this->parametersAdaptor, $v);
            }
        }
        return $params;
    }
}
