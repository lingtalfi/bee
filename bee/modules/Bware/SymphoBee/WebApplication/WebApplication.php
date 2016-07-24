<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\WebApplication;

use Bee\Abstractive\EventDispatcher\EventDispatcher;
use Bee\Abstractive\EventDispatcher\EventDispatcherInterface;
use Bee\Component\Dispatching\OrderedEventDispatcher\OrderedEventDispatcher;
use WebModule\Bware\SymphoBee\Controller\ControllerInterface;
use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use WebModule\Bware\SymphoBee\HttpResponse\HttpResponseInterface;
use WebModule\Bware\SymphoBee\WebApplication\Event\ControllerEvent;
use WebModule\Bware\SymphoBee\WebApplication\Event\ExceptionEvent;
use WebModule\Bware\SymphoBee\WebApplication\Event\RequestEvent;
use WebModule\Bware\SymphoBee\WebApplication\Event\ResponseEvent;
use WebModule\Bware\SymphoBee\WebApplication\Exception\WebApplicationException;


/**
 * WebApplication
 * @author Lingtalfi
 * 2015-02-15
 *
 */
class WebApplication implements WebApplicationInterface
{



    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(array $options = [])
    {
        $options = array_replace([
            /**
             * We can override the dispatcher (for instance if we want to log events?)
             */
            'dispatcher' => null,
        ], $options);
        $this->dispatcher = $options['dispatcher'];
        if (null === $this->dispatcher) {
            $this->dispatcher = new OrderedEventDispatcher();
        }
    }



    //------------------------------------------------------------------------------/
    // IMPLEMENTS WebApplicationInterface
    //------------------------------------------------------------------------------/
    /**
     * @return HttpResponseInterface
     */
    public function handleRequest(HttpRequestInterface $httpRequest)
    {
        $response = null;

        try {
            // request event
            $event = new RequestEvent($httpRequest);
            $this->dispatcher->dispatch('request', $event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }
            else {
                if ($event->hasControllerCallbackAndArgs()) {
                    list($controllerCallback, $arguments) = $event->getControllerCallbackAndArgs();
                    $response = $this->handleControllerCallback($controllerCallback, $arguments, $httpRequest);
                }
                else {
                    $msg = sprintf("WebApplication::handleRequest: The incoming httpRequest with path %s couldn't resolve into a controller", $httpRequest->getPath());
                    throw new WebApplicationException($msg);
                }
            }

        } catch (\Exception $e) {
            $response = $this->handleException($e, $httpRequest);
        }


        // response event 
        if (null !== $response) {
            $response = $this->filterResponse($response, $httpRequest);
        }
        return $response;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private function handleControllerCallback($controllerCallback, array $arguments, HttpRequestInterface $request)
    {
        /**
         * If the controller is a ControllerInterface object, then we dispatch the controller event.
         */
        if (is_callable($controllerCallback)) {
            if (is_array($controllerCallback) && isset($controllerCallback[0]) && $controllerCallback[0] instanceof ControllerInterface) {
                $event = new ControllerEvent($request, $controllerCallback[0]);
                $this->dispatcher->dispatch('controller', $event);
            }
        }
        else {
            $msg = "Invalid controller type: must be a callable";
            throw new WebApplicationException($msg);
        }

        // call controller
        $response = call_user_func_array($controllerCallback, $arguments);
        if (!$response instanceof HttpResponseInterface) {
            $sController = '';
            if (is_array($controllerCallback)) {
                $sController = ' (' . get_class($controllerCallback[0]) . ')';
            }
            throw new WebApplicationException(sprintf("The controller%s did not return a Response object", $sController));
        }
        return $response;
    }


    private function filterResponse(HttpResponseInterface $response, HttpRequestInterface $request)
    {
        $event = new ResponseEvent($request, $response);
        $this->dispatcher->dispatch('response', $event);
        return $event->getResponse();
    }

    private function handleException(\Exception $e, HttpRequestInterface $request)
    {
        $event = new ExceptionEvent($request, $e);
        $this->dispatcher->dispatch('exception', $event);

        // a listener might have replaced the exception
        $e = $event->getException();
        if (true === $event->hasResponse()) {
            $response = $event->getResponse();
            return $response;
        }
        throw $e;
    }

}
