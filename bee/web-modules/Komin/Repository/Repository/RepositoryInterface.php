<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Repository\Repository;



/**
 * RepositoryInterface
 * @author Lingtalfi
 * 2015-02-15
 *
 * For all methods,
 *      errors are accessible via the getErrors method,
 *      and errors are emptied at the beginning of every method call,
 *      except for the getResourceAccess which returns the errors directly.
 *
 */
interface RepositoryInterface
{

    /**
     * @return array|false
     *              the array of element's identifier (canonical name)
     *              or false in case of error.
     */
    public function getList($type);

    /**
     * @return array|false, the array of available versions for the element,
     *                      or false in case of failure.
     *
     */
    public function getVersions($type, $elementIdentifier);

    /**
     *
     *
     * @return array|false, the array of resource meta info, including the url to the zip,
     *                          or false in case of failure.
     *                          The concrete info depends on the element type.
     *
     * @param $version string|null, if the version is null, the server might choose the last version.
     *
     */
    public function getResourceInfo($type, $elementIdentifier, $version = null);


    /**
     * @returns string, the path to a valid zip file,
     *              or one of the following error messages:
     *              - E1: Resource not found with uid: $uid
     *              - E2: Not authorized: you don't have the rights to download the resource with uid: $uid and code: $code
     *              - E3: Unexpected error: please contact the webmaster
     *              
     */
    public function getResourceAccess($uid, $code = null);


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    public function getErrors();
}
