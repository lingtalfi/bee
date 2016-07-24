<?php

/**
 * This is the commandSupervisor script.
 * It uses the babyPushServer protocol to allow the main script to monitor the output of the commands.
 */
/**
 * Depends of:
 * - Bee
 * - Komin
 */
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;
use Bee\Bat\BdotTool;
use Komin\Server\AjaxTim\AjaxTimSession;
use Komin\Server\BabyPush\ServerApi\StandardServerApi;
use Komin\Server\BabyPush\Task\ShellCommandTask;

if (isset($config)) {


    //------------------------------------------------------------------------------/
    // CONFIG
    //------------------------------------------------------------------------------/
    $pipesDir = __DIR__ . '/pipes';
    require_once 'alveolus/bee/boot/autoload.php';
    require_once __DIR__ . '/../../function/command-supervisor-functions.php';

    //------------------------------------------------------------------------------/
    // SCRIPT
    //------------------------------------------------------------------------------/
    AjaxTimSession::create([
        'errorMsgPrefix' => "BabyPushServer: ",
    ])->start(function (AjaxTimSession $oSession) use ($pipesDir, $config) {

            $server = new StandardServerApi($pipesDir, [
                'ajaxTimSession' => $oSession,
                'nl2br' => true,
            ]);

            $server->setOption('getTask', function (array $taskParams) use ($config) {

                if (
                    array_key_exists('category', $taskParams) &&
                    array_key_exists('name', $taskParams)
                ) {


                    function getCommand($category, $name, $conf, array $vars)
                    {
                        $path = 'commands.' . $category . '.{x}.cmd';
                        if (null !== $cmd = BdotTool::findDotValue($path, $conf, [
                                'x' => [
                                    'name' => $name,
                                ],
                            ])
                        ) {
                            commandSupervisorReplaceVars($cmd, $vars);
                            return $cmd;
                        }
                        return false;
                    }

                    $conf = BabyYamlTool::parseFile($config);
                    $orgVars = (array_key_exists('vars', $conf) && is_array($conf['vars'])) ? $conf['vars'] : [];
                    $vars = [];
                    foreach ($orgVars as $k => $v) {
                        $vars['$' . $k . '$'] = $v;
                    }


                    $category = $taskParams['category'];
                    $name = $taskParams['name'];
                    if (false !== $cmd = getCommand($category, $name, $conf, $vars)) {
                        return new ShellCommandTask($cmd);
                    }
                    else {
                        throw new \RuntimeException(sprintf("Command not found with category: %s and name: %s", $category, $name));
                    }
                }
                else {
                    throw new \RuntimeException("Missing category and/or name parameter(s)");
                }
            })->listen();
        })->output();
}