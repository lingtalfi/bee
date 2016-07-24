<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\TableInfo;


/**
 * TableInfoUtilInterface
 * @author Lingtalfi
 *
 *
 */
interface TableInfoUtilInterface
{

    /**
     * @return false|array
     */
    public function getRawScan($db, $table);

    /**
     * @return false|array
     */
    public function getRowIdentifyingFields($db, $table);

    /**
     * @return false|array, false in case of failure.
     *              If one of the rif is missing, this method returns false.
     */
    public function getRowIdentifyingValues($db, $table, array $values);
}
