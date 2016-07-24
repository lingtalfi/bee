<?php



//------------------------------------------------------------------------------/
// REMOTE BASH SCRIPT
//------------------------------------------------------------------------------/
/**
 * LingTalfi 2014-11-03
 *
 * This script works along with the bee framework.
 * It assumes that you have modified your php.ini include_path and added a path to the bee directory
 * that contains the alveolus.
 *
 *
 *
 * You should execute this script as root, because it may execute commands like chown and chmod.
 *
 *
 * To call this script, you need to pass at least 3 arguments:
 *
 *      - machine
 *      - application
 *      - commandSet
 *
 * Use the following example to get started:
 *
 *      sudo php -f "/path/to/this/script.php" -- --machine=local --app=lingtalfi --cs=commandSet0
 *
 *
 *
 */


//------------------------------------------------------------------------------/
// SCRIPT
//------------------------------------------------------------------------------/
use Komin\Log\SuperLogger\FeeSuperLogger;
use Komin\Server\RemoteShell\Command\ChmodCommand;
use Komin\Server\RemoteShell\Command\ChownCommand;
use Komin\Server\RemoteShell\Command\LinksCommand;
use Komin\Server\RemoteShell\RemoteShellProcessor;


$file = 'bee/alveolus/bee/boot/autoload.php';
if (false !== stream_resolve_include_path($file)) {

    require_once $file;


    FeeSuperLogger::init([
        'file' => __DIR__ . '/remoteshell.log',
    ]);


    $options = getopt("", [
        'machine:',
        'app:',
        'cs:',
    ]);

    if (array_key_exists('machine', $options)) {
        if (array_key_exists('app', $options)) {
            if (array_key_exists('cs', $options)) {

                $machine = $options['machine'];
                $app = $options['app'];
                $commandSet = $options['cs'];


                $dir = __DIR__;
                $proc = new RemoteShellProcessor([
                    'dir' => $dir,
                    'commands' => [
                        new ChmodCommand(),
                        new ChownCommand(),
                        new LinksCommand(),
                    ],
                ]);
                $proc->applyCommands($machine, $app, $commandSet);

            }
            else {
                echo "cs argument (commandSet) is missing";
            }
        }
        else {
            echo "app argument (application) is missing";
        }
    }
    else {
        echo "machine argument is missing";
    }
}
else {
    echo "this script assumes that the alveolus can be found using your include_path, but this is not the case; aborting process" . PHP_EOL;
    echo sprintf("You include path is: %s", ini_get("include_path"));
}

