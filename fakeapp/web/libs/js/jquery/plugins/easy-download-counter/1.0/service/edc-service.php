<?php


//------------------------------------------------------------------------------/
// CONFIGURATION (that's where you can put your own values)
//------------------------------------------------------------------------------/
require_once __DIR__ . '/../function/functions.php';

$conf = getGrappinConf(dirname($_SERVER['SCRIPT_FILENAME']) . "/..");
$statsDir = $conf['statsDir'];

//------------------------------------------------------------------------------/
// SCRIPT (you shouldn't have to touch anything)
//------------------------------------------------------------------------------/
/**
 * This script returns "ok" if everything is fine and the counter could be updated,
 * or any other string will be considered as an error message
 */

$error = "invalid call, missing arguments";
if (isset($_POST['edcId'])) {
    //------------------------------------------------------------------------------/
    // MAIN SERVICE FOR INDIVIDUAL INCREMENTATION
    //------------------------------------------------------------------------------/
    $error = null;
    $id = $_POST['edcId'];
    $year = date("Y");
    $month = date("m");

    $file = $statsDir . '/' . $id . '/' . $year . '/' . $month . '.txt';
    if (!is_file($file)) {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                $error = "Could not create the directory $dir";
            }
        }
        if (null === $error && false === touch($file)) {
            $error = "Could not create the file $dir";
        }
    }
    if (null === $error && file_exists($file)) {
        $content = file_get_contents($file);
        $isEmpty = ('' === trim($content));
        $date = date("Y-m-d");
        if (false === strpos($content, $date)) {
            if (false === $isEmpty) {
                $content .= PHP_EOL;
            }
            $content .= $date . ":1";
        }
        else {
            $pattern = '!^' . $date . ':([0-9]+)$!';
            if (preg_match($pattern, $content, $matches)) {
                $currentCpt = $matches[1];
                $currentCpt++;
                $replacement = $date . ':' . $currentCpt;
                $content = preg_replace($pattern, $replacement, $content);
            }
            else {
                $content = $date . ':1';
            }
        }
        if (null === $error) {
            if (false !== file_put_contents($file, $content)) {

                // at this point the counter is updated
                // now let's add a file that contains all the stats since the beginning,
                // that's for the counter part
                $sumFile = $statsDir . '/' . $id . '.txt';
                $n = 1;
                if (file_exists($sumFile)) {
                    $n = file_get_contents($sumFile);
                    $n = (int)$n;
                    $n++;
                }
                file_put_contents($sumFile, $n);
            }
            else {
                $error = "couldn't update the file $file";
            }
        }
    }
    else {
        if (null === $error) {
            $error = "the file '$file'' could not be created";
        }
    }
}
elseif (isset($_POST['edcCptIds'])) {
    //------------------------------------------------------------------------------/
    // JSON SERVICE CALLED FOR COUNTERS INITIALIZATION
    //------------------------------------------------------------------------------/
    // null means error
    $ids = $_POST['edcCptIds'];
    $id2Total = array();
    foreach ($ids as $id) {
        $sumFile = $statsDir . '/' . $id . '.txt';
        $n = null;
        if (file_exists($sumFile)) {
            $n = file_get_contents($sumFile);
        }
        $id2Total[$id] = $n;
    }
    echo json_encode($id2Total);
    exit;
}
if ($error) {
    echo $error;
}
else {
    echo "ok";
}

