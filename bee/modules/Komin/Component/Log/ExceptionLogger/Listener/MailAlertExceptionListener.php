<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komin\Component\Log\ExceptionLogger\Listener;

use Bee\Component\Mail\SmtpMailer\SmtpClientInterface;


/**
 * MailAlertExceptionListener
 * @author Lingtalfi
 * 2015-25-05
 *
 */
class MailAlertExceptionListener extends BaseMailAlertExceptionListener
{

    public function __construct(SmtpClientInterface $client, $from, $recipients, $subject = null, $message = null)
    {
        parent::__construct();
        $this
            ->setFrom($from)
            ->setSmtpClient($client)
            ->setRecipients($recipients);
        if (null !== $subject) {
            $this->setSubject($subject);
        }
        if (null !== $message) {
            $this->setFormat($message);
        }
    }


}
