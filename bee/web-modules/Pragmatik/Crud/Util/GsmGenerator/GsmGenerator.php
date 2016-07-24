<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Pragmatik\Crud\Util\GsmGenerator;

use WebModule\Pragmatik\Crud\Util\RawScan2GsmAdaptor\RawScan2Gsm2Adaptor;
use WebModule\Pragmatik\Crud\Util\RawScan2GsmAdaptor\RawScan2GsmAdaptorInterface;
use WebModule\Pragmatik\Crud\Util\TableInfo\Stazy\StazyTableInfoUtil;


/**
 * GsmGenerator
 * @author Lingtalfi
 * 2015-02-07
 *
 */
class GsmGenerator implements GsmGeneratorInterface
{

    protected $options;
    /**
     * @var RawScan2GsmAdaptorInterface
     */
    protected $rawScan2GsmAdaptor;

    public function __construct(RawScan2GsmAdaptorInterface $rawScan2GsmAdaptor = null, array $options = [])
    {
        $this->options = array_replace([
            'cacheDir' => '/tmp/pragmatik/gsmGenerator',
        ], $options);
        if (null === $rawScan2GsmAdaptor) {
            $rawScan2GsmAdaptor = new RawScan2Gsm2Adaptor();
        }
        $this->rawScan2GsmAdaptor = $rawScan2GsmAdaptor;
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS GsmGeneratorInterface
    //------------------------------------------------------------------------------/
    /**
     * @return array|false in case of failure
     */
    public function generate($db, $table)
    {
        if (false !== $rawScan = StazyTableInfoUtil::getInst()->getRawScan($db, $table)) {
            return $this->rawScan2GsmAdaptor->getGsm($rawScan);
        }
        return false;
    }



}
