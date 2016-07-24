<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bware\SymphoBee\HttpRequest\Tool;



/**
 * PhpFilesAdaptor.
 * @author Lingtalfi
 * 2015-06-01
 * 
 * 
 * In php, here is what we obtain after a successful post of a form with files:
 * 
 * 
 * html
 *      input.name = image
 * php
 *      image => $phpFileInfo
 * 
 * Where $phpFileInfo is an array that looks like this:
 * 
 *      - name: ajax-loader.gif
 *      - type: image/gif
 *      - tmp_name: /private/var/tmp/phpcGM6Nu
 *      - error: 0
 *      - size: 3951
 * 
 * -------
 * html
 *      input.name = image
 *      input.name = image2
 * php
 *      image => $phpFileInfo
 *      image2 => $phpFileInfo
 * -------
 * 
 * html
 *      input.name = image[anyIndex]
 *      input.name = image[anyIndex2]
 * php
 *      image => 
 *          - name:
 *                  anyIndex: ajax-loader.gif          
 *                  anyIndex2: Capture d’écran 2014-11-25 à 20.04.15.png          
 *          - type:
 *                  anyIndex: image/gif          
 *                  anyIndex2: image/png          
 *          - tmp_name:
 *                  anyIndex: /private/var/tmp/phpjURYE7          
 *                  anyIndex2: /private/var/tmp/phpYIhzxP
 *          - error:
 *                  anyIndex: 0          
 *                  anyIndex2: 0
 *          - size:
 *                  anyIndex: 3951          
 *                  anyIndex2: 78619
 * 
 * 
 * If anyIndex(es) are not specified in the html, they have a default numeric value given by php (0, 1, ...).
 * 
 * -------
 * 
 * html
 *      input.name = image[pop][]
 *      input.name = image[pop][]
 *      input.name = image[pip]
 * php
 *      image => 
 *          - name:
 *                  pop: 
 *                      0: ajax-loader.gif
 *                      1: Capture d’écran 2014-11-25 à 20.04.15.png            
 *                  pip: certpic1.png          
 *          - type:
 *                  pop:
 *                      0: image/gif
 *                      1: image/png        
 *                  pip: image/png          
 *          - tmp_name:
 *                  pop:
 *                      0: /private/var/tmp/phpPmEjTG
 *                      1: /private/var/tmp/phpgIAmaQ
 *                  pip: /private/var/tmp/phpBMXr0h
 *          - error:
 *                  pop:
 *                      0: 0
 *                      1: 0    
 *                  pip: 0
 *          - size:
 *                  pop:
 *                      0: 3951
 *                      1: 78619       
 *                  pip: 15339
 * 
 * 
 * If anyIndex(es) are not specified in the html, they have a default numeric value given by php (0, 1, ...).
 * 
 * -------
 * 
 * 
 * --------------------------------------------
 * NOW WHAT THIS ADAPTOR DOES:::
 * --------------------------------------------
 * --------------------------------------------
 * html
 *      input.name = image
 * php
 *      image => $phpFileInfo
 * --------------
 * html
 *      input.name = image
 *      input.name = image2
 * php
 *      image => $phpFileInfo
 *      image2 => $phpFileInfo
 * ----------------
 * html
 *      input.name = image[anyIndex]
 *      input.name = image[anyIndex2]
 * php
 *      image =>
 *          - anyIndex: $phpFileInfo
 *          - anyIndex2: $phpFileInfo
 * ----------------
 * html
 *      input.name = image[pop][]
 *      input.name = image[pop][]
 *      input.name = image[pip]
 * php
 *      image =>
 *          - pop: 
 *              - 0: $phpFileInfo 
 *              - 1: $phpFileInfo 
 *          - pop: $phpFileInfo
 *
 */
class PhpFilesAdaptorTool
{

    public static function getFormattedFilesArray(array $phpFiles)
    {
        $ret = array();
        foreach ($phpFiles as $name => $phpFile) {
            if (is_array($phpFile)) {
                if (
                    array_key_exists('name', $phpFile) &&
                    array_key_exists('type', $phpFile) &&
                    array_key_exists('tmp_name', $phpFile) &&
                    array_key_exists('error', $phpFile) &&
                    array_key_exists('size', $phpFile)
                ) {

                    if (is_array($phpFile['name'])) { // php combined form
                        $ret[$name] = array();
                        self::parseCombinedForm($name, $ret[$name], $phpFile['name'], $phpFile['type'], $phpFile['tmp_name'], $phpFile['error'], $phpFile['size']);
                    } elseif (is_string($phpFile['name'])) { // default form
                        $ret[$name] = $phpFile;
                    }
                }
            }
        }
        return $ret;
    }


    //------------------------------------------------------------------------------/
    // 
    //------------------------------------------------------------------------------/
    private static function parseCombinedForm($name, array &$ret, array $names, array $types, array $tmpNames, array $errors, array $sizes)
    {
        foreach ($names as $k => $v) {
            if (
                is_array($v) &&
                array_key_exists($k, $types) &&
                array_key_exists($k, $tmpNames) &&
                array_key_exists($k, $errors) &&
                array_key_exists($k, $sizes)

            ) {
                $ret[$k] = array();
                self::parseCombinedForm($k, $ret[$k], $names[$k], $types[$k], $tmpNames[$k], $errors[$k], $sizes[$k]);
            } else {

                if (
                    array_key_exists($k, $types) &&
                    array_key_exists($k, $tmpNames) &&
                    array_key_exists($k, $errors) &&
                    array_key_exists($k, $sizes)
                ) {
                    if (is_numeric($k)) {
                        $ret[] = array(
                            'name' => $v,
                            'type' => $types[$k],
                            'tmp_name' => $tmpNames[$k],
                            'error' => $errors[$k],
                            'size' => $sizes[$k],
                        );
                    } elseif (is_string($k)) {
                        if (is_string($v)) {
                            $ret[$k] = array(
                                'name' => $v,
                                'type' => $types[$k],
                                'tmp_name' => $tmpNames[$k],
                                'error' => $errors[$k],
                                'size' => $sizes[$k],
                            );
                        } elseif (is_array($v)) {
                            $ret[$k] = array();
                            self::parseCombinedForm($k, $ret[$k], $names[$k], $types[$k], $tmpNames[$k], $errors[$k], $sizes[$k]);
                        }
                    }
                }
            }
        }
    }

}
