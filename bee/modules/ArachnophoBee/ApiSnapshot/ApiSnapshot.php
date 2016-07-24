<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\ApiSnapshot;

use ArachnophoBee\ApiSnapshot\Util\ApiSnapshotUtil;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;


/**
 * ApiSnapshot
 * @author Lingtalfi
 * 2015-04-30
 *
 */
class ApiSnapshot implements ApiSnapshotInterface
{

    protected $array;


    public function __construct(array $array)
    {
        $this->array = $array;
    }


    public static function fromDir($moduleRootDir, $moduleNamespace)
    {
        $o = new ApiSnapshotUtil();
        return new static($o->takeSnapShot($moduleRootDir, $moduleNamespace));
    }




    //------------------------------------------------------------------------------/
    // IMPLEMENTS ApiSnapshotInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array <apiSnapshot>
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param null|string $fileName ,
     *              if fileName is null the exportDump is returned.
     *              if fileName is a valid (not empty) fileName the exportDump will be put into the file.
     *
     *              The exportDump is a string in babyYaml format.
     *
     *
     * @return string|void
     *
     *              returns the exportDump if fileName is null,
     *              or put the exportDump in the given fileName.
     */
    public function export($fileName = null)
    {
        return BabyYamlTool::export($this->getArray(), $fileName);
    }


}
