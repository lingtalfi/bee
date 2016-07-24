<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beel\Server;


/**
 * CrudBeelServerInterface
 * @author Lingtalfi
 * 2015-01-10
 *
 */
interface CrudBeelServerInterface
{


    /**
     * @param array $params
     * @return false|array,
     *          false is returned in case of failure, and debug info should be logged
     *          In case of success, the array contains the following properties:
     *              - primary: bool, whether or not the request is of type primary
     *              - html: string, the html code for the table (if primary is true),
     *                          or just the tbody's content
     *              - values: array, the rows
     *              - ?colNames: array, the column names, only if primary is true
     *
     *
     * A primary request will generate the html code for the whole table,
     * while a sync request (the other type of request)'s html code will only correspond
     * to the tbody's content part of the html table.
     * Being able to generate those two types of request allows for more flexibility with
     * the client code.
     *
     *
     *
     */
    public function getView($table, array $params);

    /**
     * @return int, number of deleted rows
     */
    public function deleteRows($table, array $rowsRiv);
}
