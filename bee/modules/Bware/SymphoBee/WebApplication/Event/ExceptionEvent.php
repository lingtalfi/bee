<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Bware\SymphoBee\WebApplication\Event;

use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;


/**
 * ExceptionEvent
 * @author Lingtalfi
 *
 *
 */
class ExceptionEvent extends WithResponseEvent
{

    protected $exception;

    public function __construct(HttpRequestInterface $request, \Exception $exception)
    {
        parent::__construct($request);
        $this->exception = $exception;
    }


    public function getException()
    {
        return $this->exception;

    }
}
