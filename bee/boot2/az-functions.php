<?php

if (!function_exists('a')) {
    function a()
    {
        foreach (func_get_args() as $arg) {
            ob_start();
            var_dump($arg);
            $output = ob_get_clean();
            if ('1' !== ini_get('xdebug.default_enable')) {
                $output = preg_replace("!\]\=\>\n(\s+)!m", "] => ", $output);
            }
            echo '<pre>' . $output . '</pre>';
        }
    }

    function az()
    {
        call_user_func_array('a', func_get_args());
        exit;
    }


    function d($msg)
    {
        $f = '/tmp/drop.txt';
        $msg .= PHP_EOL;
        file_put_contents($f, $msg, FILE_APPEND);
    }



//    function e($cmd)
//    {
//        exec($cmd, $output, $ret);
//        if (0 === $ret) {
//            return implode(PHP_EOL, $output);
//        }
//        return false;
//    }
//
//    function v($expression, $return = false)
//    {
//        if (is_array($expression)) {
//            DebugTool::arrayExport($expression, $return, [
//                'space' => ' ',
//                'eol' => PHP_EOL,
//            ]);
//        }
//        else {
//            var_export($expression, $return);
//        }
//    }
}

