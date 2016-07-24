<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bee\Component\Log\SuperLogger\Listener;

use Bee\Bat\FileSystemTool;
use Bee\Component\Log\FileRotator\BySizeFileRotator;
use Bee\Component\Log\FileRotator\FileRotatorInterface;
use Bee\Component\Log\SuperLogger\Message\MessageInterface;


/**
 * DisplayListener
 * @author Lingtalfi
 * 2014-10-28
 */
class DisplayListener implements ListenerInterface
{

    protected $newLineChar;

    public function __construct(array $options=[])
    {
        $options = array_replace([
            'newLineChar' => '<br>',
        ],$options);
        $this->newLineChar = $options['newLineChar'];
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS ListenerInterface
    //------------------------------------------------------------------------------/
    public function parse(MessageInterface $message)
    {
        echo $this->newLineChar . "SuperLogger: [". $message->getId() ."] -- " . $message->getMessage() . $this->newLineChar;
    }


}
