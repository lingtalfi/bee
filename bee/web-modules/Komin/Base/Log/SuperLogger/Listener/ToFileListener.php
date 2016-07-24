<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebModule\Komin\Base\Log\SuperLogger\Listener;

use Bee\Component\Log\FileRotator\FileRotatorInterface;
use Bee\Component\Log\SuperLogger\Listener\ToFileListener as BToFileListener;
use Bee\Component\Log\SuperLogger\Message\MessageInterface;
use WebModule\Komin\Base\Notation\Psn\Stazy\StazyPsnResolver;
use Komin\Sound\ShellSoundPlayer\ShellSoundPlayer;


/**
 * ToFileListener
 * @author Lingtalfi
 * 2014-10-22
 *
 */
class ToFileListener extends BToFileListener
{

    protected $sound;


    public function __construct($file, FileRotatorInterface $fileRotator = null, array $options = [])
    {
        $options = array_replace([
            'sound' => null,
        ], $options);
        $this->sound = $options['sound'];
        $file = StazyPsnResolver::getInst()->getPath($file);
        parent::__construct($file, $fileRotator);
    }


    //------------------------------------------------------------------------------/
    // IMPLEMENTS ListenerInterface
    //------------------------------------------------------------------------------/
    public function parse(MessageInterface $message)
    {
        if ($this->sound) {
            ShellSoundPlayer::playSound($this->sound);
        }
        parent::parse($message);
    }

}
