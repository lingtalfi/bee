<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Http\HttpClient\Connexion;

use Bee\Component\Http\HttpClient\Request\HttpRequestInterface;


/**
 * ConnexionInterface
 * @author Lingtalfi
 * 2015-06-17
 *
 */
interface ConnexionInterface
{

    /**
     * @param HttpRequestInterface $req
     * @return string, the raw response
     */
    public function send(HttpRequestInterface $req);
    
}
