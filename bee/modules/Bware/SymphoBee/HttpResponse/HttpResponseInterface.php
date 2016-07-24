<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\HttpResponse;


/**
 * HttpResponseInterface.
 * @author Lingtalfi
 * 2015-06-01
 *
 */
interface HttpResponseInterface
{


    /**
     * Sends the headers, and displays the response body
     */
    public function send();

    /**
     * @param $content
     * @return HttpResponseInterface
     */
    public function setBody($content);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @return HttpResponseInterface
     */
    public function setHeader($string, $replace = true);

    /**
     * @return array of
     *                  0: string header
     *                  1: bool replace
     */
    public function getHeaders();

}
