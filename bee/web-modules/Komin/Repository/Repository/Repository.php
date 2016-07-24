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
 * Repository
 * @author Lingtalfi
 * 2015-02-15
 */
abstract class Repository implements RepositoryInterface
{

    protected $errors;

    public function __construct()
    {
        $this->errors = [];
    }


    abstract protected function doGetList($type);

    abstract protected function doGetVersions($type, $elementIdentifier);

    abstract protected function doGetResourceInfo($type, $elementIdentifier, $version = null);


    /**
     * @return array|false
     *              the array of element's identifier (canonical name)
     *              or false in case of error.
     */
    public function getList($type)
    {
        $this->errors = [];
        return $this->doGetList($type);
    }

    /**
     * @return array|false, the array of available versions for the element,
     *                      or false in case of failure.
     *
     */
    public function getVersions($type, $elementIdentifier)
    {
        $this->errors = [];
        return $this->doGetVersions($type, $elementIdentifier);
    }

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
    public function getResourceInfo($type, $elementIdentifier, $version = null)
    {
        $this->errors = [];
        return $this->doGetResourceInfo($type, $elementIdentifier, $version);
    }

    

    public function getErrors()
    {
        return $this->errors;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function addError($msg)
    {
        $this->errors[] = $msg;
    }

}
