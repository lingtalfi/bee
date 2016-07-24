<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\HttpRequest;

use Bee\Component\Bag\ReadOnlyBagInterface;


/**
 * HttpRequestInterface
 * @author Lingtalfi
 * 2015-06-01
 *
 *
 * This class uses the following nomenclature:
 *
 * - absoluteUrl: <scheme> <://> <host> ( <uri> (<?> <queryString>)? )? )?
 * - scheme: http|https
 * - host: the host name, without trailing slash
 * - uri: the request uri, always starting with a slash
 * - queryString: the request uri
 *
 *
 * Generally, users don't change php super arrays like POST directly,
 * they rather set a reference and update the references.
 *
 * In this implementation, properties are readonly, but they are only
 * created when the user calls the corresponding methods.
 * 
 *
 */
interface HttpRequestInterface
{


    //------------------------------------------------------------------------------/
    // BASIC REQUEST INFO
    //------------------------------------------------------------------------------/
    public function scheme();

    public function host();

    public function uri();

    public function queryString();

    //------------------------------------------------------------------------------/
    // MISCELLANEOUS REQUEST INFO
    //------------------------------------------------------------------------------/
    public function ip();

    public function port();


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    /**
     * @return ReadOnlyBagInterface
     */
    public function header();

    /**
     * @return ReadOnlyBagInterface
     */
    public function server();

    /**
     * @return ReadOnlyBagInterface
     */
    public function cookie();

    /**
     * @return ReadOnlyBagInterface
     */
    public function session();

    /**
     * @return ReadOnlyBagInterface
     */
    public function file();

    /**
     * @return ReadOnlyBagInterface
     */
    public function get();

    /**
     * @return ReadOnlyBagInterface
     */
    public function post();


}
