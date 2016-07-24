<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Beef\Server\ValidationTestServer\ValidationTest;


/**
 * ValidationTestInterface
 * @author Lingtalfi
 * 2015-02-08
 *
 */
interface ValidationTestInterface
{
    /**
     * @return true|string, true if the test is a success, the translated and tags resolved error message in case of failure.
     */
    public function execute($value, array $params);
}
