<?php


if (!isset($_beeAutoLoader)) {
    require_once 'alveolus/bee/boot/autoload.php';
}

require_once __DIR__ . '/../function/command-supervisor-functions.php';

if (defined("COMMAND_SUPERVISOR_CONF_PATH")) {
    $config = COMMAND_SUPERVISOR_CONF_PATH;
}

use Bee\Bat\ArrayTool;
use Bee\Notation\File\BabyYaml\Tool\BabyYamlTool;

if (isset($config) && isset($serviceUrl)) {


//------------------------------------------------------------------------------/
// SCRIPT (you shouldn't have to edit code below)
//------------------------------------------------------------------------------/
    if (!isset($allCatsOpen)) {
        $allCatsOpen = true;
    }
    if (!isset($allDescOpen)) {
        $allDescOpen = false;
    }

// displaying the main interface
// create the right menu first
    $conf = BabyYamlTool::parseFile($config);
    $orgVars = (array_key_exists('vars', $conf) && is_array($conf['vars'])) ? $conf['vars'] : [];
    $vars = [];
    foreach ($orgVars as $k => $v) {
        $vars['$' . $k . '$'] = $v;
    }

    $m = '';
    if (array_key_exists('commands', $conf) && is_array($conf['commands'])) {

        $symbolCat = '&#x229e;';
        $symbolDesc = '&#x229e;';
        $sUl = ' class="hidecats"';
        $sCat = ' plus';
        $sDesc = ' hidden';
        if (true === $allCatsOpen) {
            $symbolCat = '&#x229f;';
            $sUl = '';
            $sCat = '';
        }
        if (true === $allDescOpen) {
            $symbolDesc = '&#x229f;';
            $sDesc = '';
        }

        $m .= '<ul class="topmostul">';
        foreach ($conf['commands'] as $catName => $commands) {

            $m .= '<li><ul' . $sUl . ' data-domain="' . $catName . '">';
            $m .= '<div class="catname">';
            $m .= $catName;
            $m .= '<a class="cat-toggle' . $sCat . '" href="#">';
            $m .= $symbolCat;
            $m .= '</a>';
            $m .= '</div>';


            if (is_array($commands)) {
                foreach ($commands as $command) {
                    ArrayTool::checkKeys(['name', 'cmd'], $command, 0);
                    $cmd = $command['cmd'];
                    commandSupervisorReplaceVars($cmd, $vars);

                    $sConfirm = '';
                    if (array_key_exists('confirm', $command) && true === (bool)$command['confirm']) {
                        $sConfirm = ' confirm';
                    }


                    $m .= '<li class="cmd-trigger'. $sConfirm .'" data-name="' . $command['name'] . '">';
                    $m .= '<a class="cmd-trigger" href="#">';
                    $m .= $command['name'];
                    $m .= '</a>';
                    $m .= '<a class="description-toggle" href="#">';
                    $m .= $symbolDesc;
                    $m .= '</a>';
                    $m .= '<div class="description' . $sDesc . '">';
                    $m .= $command['description'];
                    $m .= '<hr>';
                    $m .= nl2br($cmd);
                    $m .= '</div>';
                    $m .= '</li>';

                }
            }
            $m .= '</li></ul>';
        }
        $m .= '</ul>';
    }


    ob_start();
    require_once __DIR__ . '/tpl.php';
    $tpl = ob_get_clean();
    $tpl = str_replace([
        '$rightmenu$',
        '$serviceUrl$',
    ], [
        $m,
        $serviceUrl,
    ], $tpl);
    echo $tpl;
}
else {
    echo "Bad configuration: Missing variables config and/or serviceUrl";
}