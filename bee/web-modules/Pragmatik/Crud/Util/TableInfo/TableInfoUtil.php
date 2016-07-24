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

use Bee\Application\Config\Util\FeeConfig;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;
use WebModule\Pragmatik\Crud\Tool\RawScanTool;


/**
 * TableInfoUtil
 * @author Lingtalfi
 *
 *
 */
class TableInfoUtil implements TableInfoUtilInterface
{

    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = array_replace([
            'cacheDir' => '/tmp/pragmatik/crud-tableinfoutil',
        ], $options);
    }

    //------------------------------------------------------------------------------/
    // IMPLEMENTS TableInfoUtilInterface
    //------------------------------------------------------------------------------/
    /**
     * @return false|array
     */
    public function getRawScan($db, $table)
    {
        $ftable = $db . '-' . $table;
        $cDir = $this->options['cacheDir'] . '/rawscan';
        if (null !== $cDir) {
            $cDir = StazyPsnResolver::getInst()->getPath($cDir);
            $cFile = $cDir . '/rawscan-' . $ftable . '.yml';
            if (file_exists($cFile)) {
                $rawScan = FeeConfig::readFile($cFile);
            }
            else {
                if (false !== $rawScan = RawScanTool::getRawScan($db, $table)) {
                    BabyYamlTool::write($cFile, $rawScan);
                }
            }
        }
        else {
            $rawScan = RawScanTool::getRawScan($db, $table);
        }
        return $rawScan;
    }

    /**
     * @return false|array
     */
    public function getRowIdentifyingFields($db, $table)
    {
        $ret = false;
        $ftable = $db . '.' . $table;
        $cDir = $this->options['cacheDir'] . '/rif';
        $file = $cDir . '/' . $ftable . '.yml';
        if (file_exists($file)) {
            $ret = BabyYamlTool::parseFile($file);
        }
        else {
            if (false !== $rawScan = $this->getRawScan($db, $table)) {
                $ret = [];
                foreach ($rawScan as $control) {
                    if (true === $control['ai']) {
                        $ret = [$control['column']];
                        break;
                    }
                    $ret[] = $control['column'];
                }
            }
        }
        return $ret;
    }

    /**
     * @return false|array
     */
    public function getRowIdentifyingValues($db, $table, array $values)
    {
        $ret = false;
        if (false !== $rif = $this->getRowIdentifyingFields($db, $table)) {
            $ret = [];
            foreach ($rif as $key) {
                if (array_key_exists($key, $values)) {
                    $ret[$key] = $values[$key];
                }
                else {
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }


}
