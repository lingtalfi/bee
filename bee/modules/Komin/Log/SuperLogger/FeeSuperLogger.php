<?php

/*
 * This file is part of the Bee package.
 *
 * (c) Ling Talfi <lingtalfi@bee-framework.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Komin\Log\SuperLogger;

use Bee\Component\Log\FileRotator\BySizeFileRotator;
use Bee\Component\Log\SuperLogger\Listener\DisplayListener;
use Bee\Component\Log\SuperLogger\Listener\PlaySoundListener;
use Bee\Component\Log\SuperLogger\Listener\ToFileListener;
use Bee\Component\Log\SuperLogger\SuperLogger;
use Komin\Sound\ShellSoundPlayer\ShellSoundPlayer;


/**
 * FeeSuperLogger
 * @author Lingtalfi
 * 2014-10-28
 *
 */
class FeeSuperLogger
{

    public static function init(array $options = [])
    {

        $options = array_replace([
            'verbose' => false, // use DisplayListener?

            // fileListener
            'file' => null, // change this to a file path to use ToFileListener
            'fileMaxSize' => '100 Mo',

            // SoundListener?
//            'sound' => 'sonar.mp3',
//            'sound' => 'beep.mp3',
            'sound' => 'pop.mp3',
        ], $options);


        $slog = SuperLogger::getInst();
        if (null !== $options['sound']) {
            $sound = __DIR__ . '/../../_assets/sounds/' . $options['sound'];
            $slog->addListener(new PlaySoundListener(['sound' => $sound]), '*');
        }
        if (true === $options['verbose']) {
            $slog->addListener(new DisplayListener(), '*');
        }
        if (null !== $file = $options['file']) {
            $rot = new BySizeFileRotator(['maxSize' => $options['fileMaxSize']]);
            $lis = new ToFileListener($file, $rot);
            $slog->addListener($lis, '*');
        }
    }
}
