<?php


use Bee\Bat\FileTool;


require_once 'alveolus/bee/boot/autoload.php';


//------------------------------------------------------------------------------/
// FETCH THE TEMPLATE
//------------------------------------------------------------------------------/
if (isset($_GET['callback'])) {
    $callback = $_GET['callback'];
    

    $tpl = 'default';
    if (isset($_GET['tpl'])) {
        $tpl = $_GET['tpl'];
    }
    $tplDir = __DIR__ . '/../tpl';
    $file = $tplDir . '/' . $tpl . '/skeleton.html';
    $html = '';
    $cssPath = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
    $end = '/server/json.php';
    $cssPath = str_replace($end, '/tpl/' . $tpl . '/style.css', $cssPath);

    if (true === FileTool::existsUnder($file, $tplDir)) {
        $html = file_get_contents($file);
    }
    $jsonOut = [
        'htmlContent' => $html,
        'cssUrl' => $cssPath,
    ];

    //------------------------------------------------------------------------------/
    // WRITING THE OUTPUT
    //------------------------------------------------------------------------------/
    echo $callback . ' ('. json_encode($jsonOut) .');';
}