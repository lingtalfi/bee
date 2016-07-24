<?php
use Komin\Server\BabyPush\Task\TaskInterface;


/**
 * @var TaskInterface $oTask
 */
$eol = '<br>';

$oTask->feedPipe("Guessing the website type...");
sleep(1);
$oTask->feedPipe("... ok (e-commerce assumed)" . $eol);


$oTask->feedPipe("Building the products catalog...");
sleep(2);
$oTask->feedPipe("... ok (1530 products added)" . $eol);




$oTask->feedPipe("Choosing a template...");
sleep(1);
$oTask->feedPipe("... ok (mint selected)" . $eol);


$oTask->feedPipe("Building the website...");
sleep(3);
$oTask->feedPipe("... ok" . $eol);


$oTask->feedPipe("Exporting the website as a .zip ...");
sleep(2);
$oTask->feedPipe("... ok" . $eol);

$oTask->feedPipe("You can now download the website at the following address (http://www.i_am_a_lier.com/mysite)" . PHP_EOL);




