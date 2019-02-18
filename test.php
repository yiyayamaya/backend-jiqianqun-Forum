<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('configuration/load.php');

$user_id = 3;
$script_id = 1;
$result = "This is a result";
$iter = 5;
$time = 30;
$num_iter = 0;

$result_id = 1;

for ($i = 0; $i < $iter; $i++) {
    $insert = $db->insert("script_result",
        array(
            "resultID" => 1,
            "userID" => $user_id,
            "scriptID" => $script_id,
            "iterationID" => $num_iter++,
            "result" => $result
        )
    );
    sleep($time);
}