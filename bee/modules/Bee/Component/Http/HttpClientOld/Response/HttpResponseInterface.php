<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Http\HttpClient\Response;

use Bee\Component\Bag\CaseInsensitiveReadOnlyBagInterface;
use Bee\Component\Bag\ReadOnlyBagInterface;


/**
 * HttpResponseInterface
 * @author Lingtalfi
 * 2015-06-11
 *
 */
interface HttpResponseInterface
{

    public function getHttpVersion();

    public function getStatusCode();

    public function getReasonPhrase();
    

    /**
     * @return CaseInsensitiveReadOnlyBagInterface
     */
    public function headers();

    /**
     * @return ReadOnlyBagInterface
     */
    public function cookies();

    public function getContentType();
    
    public function getBody();
}
