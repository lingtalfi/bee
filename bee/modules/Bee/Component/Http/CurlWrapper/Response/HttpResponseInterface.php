<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Http\CurlWrapper\Response;

use Bee\Component\Bag\ReadOnlyBagInterface;


/**
 * HttpResponseInterface
 * @author Lingtalfi
 * 2015-06-10
 *
 * A response that some wrappers will return.
 *
 */
interface HttpResponseInterface
{

    /**
     * @return string
     */
    public function getRaw();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @return string
     */
    public function getHeaders();
}
