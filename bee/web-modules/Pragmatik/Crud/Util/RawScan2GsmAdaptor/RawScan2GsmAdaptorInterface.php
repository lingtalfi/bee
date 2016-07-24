<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\RawScan2GsmAdaptor;


/**
 * RawScan2GsmAdaptorInterface
 * @author Lingtalfi
 * 2015-02-07
 *
 */
interface RawScan2GsmAdaptorInterface
{

    public function getGsm(array $rawScan);
}
