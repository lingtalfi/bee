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


/**
 * ApiSnapshotInterface
 * @author Lingtalfi
 * 2015-04-30
 *
 */
interface ApiSnapshotInterface
{

    /**
     * @return array <apiSnapshot>
     */
    public function getArray();


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
    public function export($fileName = null);

}
