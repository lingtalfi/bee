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
 * BaseRawScan2GsmAdaptor
 * @author Lingtalfi
 * 2015-02-07
 *
 */
abstract class BaseRawScan2GsmAdaptor implements RawScan2GsmAdaptorInterface
{


    /**
     * @return array with following entries
     *                  - type: the type
     *                  - params: the params if any
     */
    protected function getTypeAndParamsByRawScanEntry(array $rawScanEntry, array $rawScan)
    {
        $imageRegexes = [
            '!(?:(?:image)|(?:photo)|(?:avatar)|(?:thumb))!i',
        ];
        $colorRegexes = [
            '!(color)!i',
        ];


        $ret = [];
        $type = $rawScanEntry['type'];
        $params = [];

        if (true === $rawScanEntry['ai']) {
            $ret['type'] = 'inputHidden';
        }
        else {

            //------------------------------------------------------------------------------/
            // SPECIAL TYPES: image, color, list
            //------------------------------------------------------------------------------/
            $typeFound = false;
            foreach ($imageRegexes as $r) {
                if (preg_match($r, $rawScanEntry['column'])) {
                    $ret['type'] = 'image';
                    $typeFound = true;
                }
            }
            foreach ($colorRegexes as $r) {
                if (preg_match($r, $rawScanEntry['column'])) {
                    $ret['type'] = 'color';
                    $typeFound = true;
                }
            }

            if (null !== $rawScanEntry['fk']) {
                $ret['type'] = 'select';


                $p = explode(':', $rawScanEntry['fk'], 2);
                $table = $p[0];
                $itemFormat = $this->getFriendlyFkItemFormat($p[0], $p[1], $rawScan);
                $key = null;
                foreach ($rawScan as $r) {
                    if (true === $r['ai']) {
                        $key = $r['column'];
                        break;
                    }
                }
                if (null === $key) {
                    reset($rawScan);
                    $r = current($rawScan);
                    $key = $r['column'];
                }
                $params['_pdo'] = $table . ':' . $key . ':' . $itemFormat;
                $typeFound = true;
            }


            //------------------------------------------------------------------------------/
            // REGULAR TYPES: text, int, bool, date, datetime
            //------------------------------------------------------------------------------/
            /**
             * Todo: implement special types like date in gsm1
             */
            if (false === $typeFound) {
                switch ($type) {
                    case 'varchar':
                    case 'text':
                        $ret['type'] = 'inputText';
                        break;
                    case 'char':
                        if (
                            1 === (int)$ret['length'] &&
                            preg_match('!(?:(?:is)|(?:has))([A-Z]|_)!', $ret['column'])
                        ) {
                            $ret['type'] = 'bool';
                        }
                        else {
                            $ret['type'] = 'inputText';
                        }
                        break;
                    case 'int':
                    case 'tinyint':
                    case 'decimal':
                        $ret['type'] = 'int';
                        $ret['type'] = 'inputText';
                        break;
                    case 'date':
                        $ret['type'] = 'date';
                        $ret['type'] = 'inputText';
                        break;
                    case 'datetime':
                        $ret['type'] = 'datetime';
                        $ret['type'] = 'inputText';
                        break;
                    default;
                        throw new \RuntimeException(sprintf("Unknown type: %s", $type));
                        break;
                }
            }
        }

        $ret['params'] = $params;
        return $ret;
    }

    protected function getFriendlyFkItemFormat($table, $defaultColumn, array $rawScan)
    {
        $ret = '{' . $defaultColumn . '}';
        if ($rawScan) {
            $id = null;
            $title = null;
            $fvchar = null; // first var char
            $titles = [
                'name',
                'title',
                'label',
            ];
            foreach ($rawScan as $info) {
                $key = $info['column'];
                if (true === $info['ai']) {
                    $id = $key;
                }
                elseif (in_array($key, $titles)) {
                    $title = $key;
                    break;
                }
                elseif (null === $fvchar && 'varchar' === $info['type']) {
                    $fvchar = $key;
                }
            }
            if ($title) {
                $ret = '';
                if (null !== $id) {
                    $ret .= '{' . $id . '}. ';
                }
                if (null !== $title) {
                    $ret .= '{' . $title . '}';
                }
            }
            elseif ($fvchar) {
                $ret = '';
                if (null !== $id) {
                    $ret .= '{' . $id . '}. ';
                }
                if (null !== $fvchar) {
                    $ret .= '{' . $fvchar . '}';
                }
            }
        }
        return $ret;
    }
}
