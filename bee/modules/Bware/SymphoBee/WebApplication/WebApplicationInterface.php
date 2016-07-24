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

use WebModule\Bware\SymphoBee\HttpRequest\HttpRequestInterface;
use WebModule\Bware\SymphoBee\HttpResponse\HttpResponseInterface;


/**
 * WebApplicationInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 */
interface WebApplicationInterface
{

    /**
     * @return HttpResponseInterface
     */
    public function handleRequest(HttpRequestInterface $httpRequest);
}
