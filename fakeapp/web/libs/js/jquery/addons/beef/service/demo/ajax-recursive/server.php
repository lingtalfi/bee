<?php



require_once 'alveolus/bee/boot/autoload.php';


$values = [];
if (isset($_POST['values'])) {
    $values = $_POST['values'];
}

$m = [
    'html' => file_get_contents('form.html'),
    'js' =>  str_replace('$values$', json_encode($values), file_get_contents('js.js')),
];


$ret = [
    't' => 's',
    'm' => $m,
];


echo json_encode($ret);