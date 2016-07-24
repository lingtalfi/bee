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
 * RawScan2Gsm2Adaptor
 * @author Lingtalfi
 * 2015-02-12
 *
 */
class RawScan2Gsm2Adaptor extends BaseRawScan2GsmAdaptor
{

    //------------------------------------------------------------------------------/
    // IMPLEMENTS RawScan2GsmAdaptorInterface
    //------------------------------------------------------------------------------/
    public function getGsm(array $rawScan)
    {
        $ret = [];
        foreach ($rawScan as $entry) {
            $ret[$entry['column']] = $this->getGsmNode($entry, $rawScan);
        }
        return $ret;
    }

    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    protected function getGsmNode(array $rawScanEntry, array $rawScan)
    {
        $ret = $this->getTypeAndParamsByRawScanEntry($rawScanEntry, $rawScan);
        return $ret;
    }

}
