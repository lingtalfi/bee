<?php


//------------------------------------------------------------------------------/
// MODULE STANDARD FUNCTIONS
//------------------------------------------------------------------------------/
/**
 * 2014-10-27
 * by LingTalfi
 *
 * Just copy paste this file for every new module you create.
 * Or, if you are a bee user and you are not a fan of copy paste,
 *      you can alternatively use the GrappinBooter from the Ling package (Ling\Pattern\Grappin\GrappinBooter)
 *      to import the functions in your workspace.
 * 
 * 
 * The grappin technique allows us to reference the module with a symlink, while being
 * able to access its configuration from an external application.
 * Search for [grappin™] pattern.
 *
 */


if (!function_exists('getGrappinConf')) {
    /**
     * Grappin conf, just copy paste in your new modules.
     */

    function grappinGetAbsolutePath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $startSlash = false;
        if (DIRECTORY_SEPARATOR === substr($path, 0, 1)) {
            $startSlash = true;
        }
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            }
            else {
                $absolutes[] = $part;
            }
        }
        $ret = implode(DIRECTORY_SEPARATOR, $absolutes);
        if (true === $startSlash) {
            $ret = DIRECTORY_SEPARATOR . $ret;
        }
        return $ret;
    }

    function getGrappinConf($rootDir, $fileName = null)
    {
        $rootDir = grappinGetAbsolutePath($rootDir);
        if (file_exists($rootDir)) {

            if (null === $fileName) {
                $fileName = 'conf';
            }
            $confFile = $rootDir . '/' . $fileName . '.php';
            if (file_exists($confFile)) {

                $_conf = null;
                require_once $confFile;
                if (!is_array($_conf)) {
                    throw new \RuntimeException("Your conf file must contain an array");
                }
                $ret = $_conf;

                $_conf = null;
                $devFile = dirname($rootDir) . '/' . $fileName . '.php';
                if (file_exists($devFile)) {
                    require_once $devFile;
                }
                if (is_array($_conf)) {
                    $ret = array_replace($ret, $_conf);
                }
                return $ret;
            }
            else {
                throw new \RuntimeException("Are you kidding me? where is your conf file?");
            }
        }
        else {
            throw new \RuntimeException(sprintf("Root dir doesn't exist: %s", $rootDir));
        }
    }
}


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
}


