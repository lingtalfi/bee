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

use Bee\Application\Config\Util\FeeConfig;
use Bee\Component\Log\SuperLogger\Traits\SuperLoggerTrait;
use Bee\Component\FileSystem\Finder\FileInfo\FinderFileInfo;
use Bee\Component\FileSystem\Finder\Finder;


/**
 * FileRepository
 * @author Lingtalfi
 * 2015-02-15
 *
 * With this repository, the whole organization is the tree structure, files are organized as following:
 *
 *
 * - $rootDir:
 * ----- $type:
 * --------- $identifier:
 * ------------- versions:
 * ----------------- $version:
 * --------------------- $identifier-$version.yml
 * --------------------- $identifier-$version.zip
 *
 * With:
 *      $rootDir: string, the path to an arbitrary root dir
 *      $type: string, the type of the resource
 *      $identifier: string, the resource identifier (canonical name), unique per type.
 *      $version: string, the resource version
 *
 * This organization allows us to preserver some room for further organization.
 * For instance, we can create a sources folder next to the versions folder if needed.
 *
 */
class FileRepository extends Repository
{

    use SuperLoggerTrait;

    protected $params;

    public function __construct(array $params = [])
    {
        parent::__construct();
        $this->params = array_replace([
            'rootDir' => null,
            /**
             * a string, $uid is replaced by a server generated value
             *      For instance, http://example.com/getresource.php?uid=$uid
             */
            'getResourceUrlDefaultFormat' => '',
        ], $params);
    }


    protected function doGetList($type)
    {
        $ret = false;
        if (true === $this->checkTypeDir($type)) {
            $ret = [];
            $typeDir = $this->params['rootDir'] . '/' . $type;
            Finder::create($typeDir)->directories()->maxDepth(0)->find(function (FinderFileInfo $file) use (&$ret) {
                $ret[] = $file->getFilename();
            });
        }
        return $ret;
    }

    protected function doGetVersions($type, $elementIdentifier)
    {
        $ret = false;
        if (true === $this->checkIdentifierDir($elementIdentifier, $type)) {
            $ret = [];
            $versionsDir = $this->params['rootDir'] . '/' . $type . '/' . $elementIdentifier . '/versions';
            if (is_dir($versionsDir)) {
                Finder::create($versionsDir)->directories()->maxDepth(0)->find(function (FinderFileInfo $file) use (&$ret) {
                    $ret[] = $file->getFilename();
                });
            }
        }
        return $ret;
    }

    protected function doGetResourceInfo($type, $elementIdentifier, $version = null)
    {
        if (null === $version) {
            if (false !== $versions = $this->doGetVersions($type, $elementIdentifier)) {
                if (count($versions) > 0) {
                    $version = array_pop($versions);
                }
                else {
                    $this->addError(sprintf("no version of %s %s is available", $type, $elementIdentifier));
                }
            }
            else {
                return false;
            }
        }
        if (null !== $version) {
            $file = $this->params['rootDir'] . '/' . $type . '/' . $elementIdentifier . '/versions/' . $version . '/' . $elementIdentifier . '-' . $version . '.yml';
            if (file_exists($file)) {
                $ret = FeeConfig::readFile($file);
                $this->completeResourceInfo($ret, $type, $elementIdentifier, $version);
                return $ret;
            }
            else {
                if (false !== $versions = $this->doGetVersions($type, $elementIdentifier)) {
                    $this->addError(sprintf("not found: info for %s %s with version %s", $type, $elementIdentifier, $version));
                }
            }
        }
        return false;
    }


    protected function doHasResourceAccess($uid, $code = null)
    {
    }

    /**
     * @returns string, the path to a valid zip file,
     *              or one of the following error messages:
     *              - E1: Resource not found with uid: $uid
     *              - E2: Not authorized: you don't have the rights to download the resource with uid: $uid and code: $code
     *              - E3: Unexpected error: please contact the webmaster
     *
     */
    public function getResourceAccess($uid, $code = null)
    {
        $ret = null;
        try {
            list($type, $elementIdentifier, $version) = $this->getUidInfo($uid);
            $file = $this->params['rootDir'] . '/' . $type . '/' . $elementIdentifier . '/versions/' . $version . '/' . $elementIdentifier . '-' . $version . '.zip';
            if (file_exists($file)) { // should maybe check if the file is readable/executable?
                if (true === $this->hasResourceAccess($uid, $code)) {
                    $ret = realpath($file);
                }
                else {
                    $ret = sprintf("E2: Not authorized: you don't have the rights to download the resource with uid: %s and code: %s", $uid, (string)$code);
                }
            }
            else {
                $ret = sprintf('E1: Resource not found with uid: %s', $uid);
            }
        } catch (\Exception $e) {
            $ret = 'E3: Unexpected error: please contact the webmaster';
            $this->slog("unexpectedResourceAccessError", $e);
        }
        return $ret;
    }



    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function checkTypeDir($type)
    {
        if (is_dir($this->params['rootDir'] . '/' . $type)) {
            return true;
        }
        $this->addError(sprintf("unknown resource type: %s", $type));
        return false;
    }

    protected function checkIdentifierDir($identifier, $type)
    {
        if (is_dir($this->params['rootDir'] . '/' . $type . '/' . $identifier)) {
            return true;
        }
        elseif (false !== $this->checkTypeDir($type)) {
            $this->addError(sprintf("unknown identifier: %s", $identifier));
        }
        return false;
    }

    protected function getUid($type, $elementIdentifier, $version)
    {
        return $type . ':' . $elementIdentifier . ':' . $version;
    }


    /**
     * This method returns an array containing the type, identifier and version from a given uid.
     * This is the opposite of the getUid method.
     */
    protected function getUidInfo($uid)
    {
        return explode(':', $uid);
    }

    protected function completeResourceInfo(array &$info, $type, $elementIdentifier, $version)
    {
        if (!array_key_exists('resourceUrl', $info) && $this->params['getResourceUrlDefaultFormat']) {
            $uid = $this->getUid($type, $elementIdentifier, $version);
            $info['resourceUrl'] = str_replace('$uid', $uid, $this->params['getResourceUrlDefaultFormat']);
        }
    }

    protected function hasResourceAccess($uid, $code = null)
    {
        return true;
    }

}
