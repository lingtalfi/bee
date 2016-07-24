<?php
use Komin\Server\AjaxTim\AjaxTimSession;
use Komin\Server\BabyPush\ServerApi\StandardServerApi;
use Komin\Server\BabyPush\Task\PhpFileTask;
use Komin\Server\BabyPush\Task\ShellCommandTask;

/**
 * This is an implementation of BabyPush protocol,
 * using ajaxtim as a wrapper for errors.
 */
//------------------------------------------------------------------------------/
// CONFIG
//------------------------------------------------------------------------------/
// make sure $pipesDir is writable by the server
$pipesDir = __DIR__ . '/pipes';
$tasksDir = __DIR__ . '/tasks';


//------------------------------------------------------------------------------/
// SCRIPT (you shouldn't have to edit below)
//------------------------------------------------------------------------------/
/**
 * Depends of:
 * - Bee
 * - Komin
 */
require_once 'alveolus/bee/boot/autoload.php';


AjaxTimSession::create([
    'errorMsgPrefix' => "BabyPushServer: ",
])->start(function (AjaxTimSession $oSession) use ($pipesDir, $tasksDir) {

        $server = new StandardServerApi($pipesDir, [
            'ajaxTimSession' => $oSession,
        ]);
        $server->setOption('getTask', function (array $taskParams) use ($tasksDir) {

            if (array_key_exists('taskId', $taskParams)) {
                $taskId = $taskParams['taskId'];
                $actionFile = $tasksDir . '/' . $taskId . '.php';
                if (file_exists($actionFile)) {
                    return new PhpFileTask($actionFile);
                }
                else {
                    $actionFile = $tasksDir . '/' . $taskId . '.sh';
                    if (file_exists($actionFile)) {
                        $cmd = '"' . $actionFile . '" 2>&1';
                        return new ShellCommandTask($cmd);
                    }
                    else {
                        throw new \RuntimeException(sprintf("Cannot find the action file with taskId: %s", $taskId));
                    }
                }
            }
            else {
                throw new \RuntimeException("taskId parameter missing");
            }

        })->listen();
    })->output();



