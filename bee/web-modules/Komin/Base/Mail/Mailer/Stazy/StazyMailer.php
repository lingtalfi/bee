<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Mail\Mailer\Stazy;

use Bee\Component\Mail\Mailer\MailerInterface;
use WebModule\Komin\Base\Container\Stazy\StazyContainer;


/**
 * StazyMailer
 * @author Lingtalfi
 * 2014-10-12
 *
 */
class StazyMailer
{


    private static $inst;


    private function __construct()
    {
    }


    /**
     * @return MailerInterface
     */
    public static function getInst()
    {
        if (null === self::$inst) {
            self::$inst = StazyContainer::getInst()->getService('komin.base.mail.mailer');
        }
        return self::$inst;
    }

}
