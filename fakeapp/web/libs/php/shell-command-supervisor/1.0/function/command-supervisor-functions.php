<?php



function commandSupervisorGetConfPath($default)
{
    if (defined('COMMAND_SUPERVISOR_CONF_PATH')) {
        return COMMAND_SUPERVISOR_CONF_PATH;
    }
    return $default;
}


function commandSupervisorReplaceVars(&$str, array $vars)
{
    $str = str_replace(array_keys($vars), array_values($vars), $str);
}