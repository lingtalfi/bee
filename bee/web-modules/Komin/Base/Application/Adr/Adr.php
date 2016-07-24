<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Application\Adr;

use Bee\Component\Log\SuperLogger\SuperLogger;


/**
 * Adr
 * @author Lingtalfi
 * 2014-10-21 --> 2015-01-13
 *
 */
class Adr implements AdrInterface
{

    protected $libs;
    private $_cache;

    public function __construct(array $libs = [])
    {
        $this->libs = $libs;
    }


    public function resolve(array $libIds, array $customAssets = [])
    {
        if (null === $this->_cache) {


            $end = [];
            $resolved = [];
            $id2Deps = [];

            /**
             * First, let's merge $customAssets with libIds
             */
            if ($customAssets) {
                $c = 0;
                foreach ($customAssets as $inf) {
                    list($assets, $libs) = $inf;
                    $libId = '_custom' . $c++;
                    $this->libs[$libId] = [
                        'assets' => $assets,
                    ];
                    if ($libs) {
                        $this->libs[$libId]['libs'] = $libs;
                    }
                }
            }


            /**
             * This loop will end up with the id2Deps, an array containing id => ordered (deepest first) dependencies libraries
             */
            foreach ($libIds as $id) {
                if (array_key_exists($id, $this->libs)) {
                    $deps = [];
                    $this->doResolve($id, $this->libs[$id], $resolved, $deps, 0);
                    $id2Deps[$id] = $deps;
                }
            }


            /**
             * Now we put the ordered libraries in a return array
             */
            foreach ($id2Deps as $id => $deps) {
                if ($deps) {
                    foreach ($deps as $dep) {
                        $end[$dep] = $this->libs[$dep]['assets'];
                    }
                }
                $end[$id] = $this->libs[$id]['assets'];
            }

            /**
             * Last but not least, we separate css from js.
             */
            $this->_cache = [
                'css' => [],
                'js' => [],
            ];
            foreach ($end as $assets) {
                foreach ($assets as $asset) {
                    if ('.js' === substr($asset, -3)) {
                        $this->_cache['js'][] = $asset;
                    }
                    elseif ('.css' === substr($asset, -4)) {
                        $this->_cache['css'][] = $asset;
                    }
                }
            }
        }
        return $this->_cache;
    }


    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function doResolve($libId, array $lib, array &$resolved, array &$deps, $depth = 0)
    {
        if (false === in_array($libId, $resolved, true)) {
            if (array_key_exists('libs', $lib) && $lib['libs']) {
                foreach ($lib['libs'] as $_id) {
                    if (array_key_exists($_id, $this->libs)) {
                        $this->doResolve($_id, $this->libs[$_id], $resolved, $deps, $depth + 1);
                        $resolved[] = $_id;
                    }
                    else {
                        $this->error("Library with id=%s calls non existing library with id=%s", $libId, $_id);
                    }
                }
            }
            if ($depth > 0) {
                $deps[] = $libId;
            }
        }
    }

    private function error($m)
    {
        SuperLogger::getInst()->log('Komin.Base.Application.Adr', $m);
    }

}
