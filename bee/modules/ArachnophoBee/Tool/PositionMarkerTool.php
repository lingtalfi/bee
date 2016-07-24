<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArachnophoBee\Tool;


/**
 * PositionMarkerTool
 * @author Lingtalfi
 * 2015-04-10
 *
 * A position marker is an object that contains the numeric starting and ending positions of an element.
 * This is a rather abstract definition, and therefore hopefully it can be reused in many contexts.
 *
 *      - positionMarker
 *              0: int, start position
 *              1: int, end position
 *
 *
 */
class PositionMarkerTool
{

    /**
     * linear position marker would be an array of position markers that we obtained by linearly processing
     * a structure that may contain recursion.
     *
     * For instance parsing array structures in the following code, from left to right:
     *
     *              a[ b[ c[doo] ] ] d[s]
     *
     * we could obtain a linear representation that look like this:
     *
     *      - [ 0, 15 ]
     *      - [ 3, 13 ]
     *      - [ 6, 11 ]
     *      - [ 38, 41 ]
     *
     * Whereas the nested position markers form would be:
     *
     *      -
     *      ----- 0
     *      ----- 15
     *      ----- (children)
     *      ---------
     *      ------------- 3
     *      ------------- 13
     *      ------------- (children)
     *      -----------------
     *      --------------------- 6
     *      --------------------- 11
     *      --------------------- (children)
     *      -
     *      ----- 38
     *      ----- 41
     *      ----- (children)
     *
     *
     */
    public static function linearToNested(array &$positionMarkers)
    {
        array_walk($positionMarkers, function (&$el, $k) use (&$ret, &$positionMarkers) {
            $r = $el[1];
            $children = [];
            while (array_key_exists(++$k, $positionMarkers)) {
                $nextEl = $positionMarkers[$k];
                if ($nextEl[1] < $r) {
                    $children[] = $nextEl;
                    unset($positionMarkers[$k]);
                }
                else {
                    break;
                }
            }

            if ($children) {
                self::linearToNested($children);
            }
            $el[2] = $children;
        });
    }

}
