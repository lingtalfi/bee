<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\User\BadgeList;


/**
 * BadgeList
 * @author Lingtalfi
 *
 *
 */
class BadgeList implements BadgeListInterface
{
    /**
     * @var array, one dimension array
     */
    protected $badgesTree;

    public function __construct(array $badgesTree)
    {
        $this->badgesTree = $badgesTree;
    }


    public function getBadges(array $badges)
    {
        $ret = [];
        foreach ($badges as $badge) {
            $this->processBadge($ret, $badge);
        }
        return $ret;
    }

    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    private function processBadge(array &$ret, $badge)
    {
        if (!in_array($badge, $ret, true)) {
            $ret[] = $badge;
            if (array_key_exists($badge, $this->badgesTree)) {
                $subBadges = $this->badgesTree[$badge];
                foreach ($subBadges as $sub) {
                    $this->processBadge($ret, $sub);
                }
            }
        }
    }
}
