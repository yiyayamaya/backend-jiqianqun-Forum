<?php
/**
 * search.php
 *  input
 *      mode1 关键词搜索
 *      mode2 全局搜索
 *      search_key
 *      language
 *      time
 *          W - past week
 *          M - past month
 *          Y - past year
 *  output
 *      the result of the search
 *      return empty result if there is no match
 */

require_once("configuration/load.php");


$allowed_time = ["W", "M", "Y"];

if (isset($_POST["mode"])) {

switch ($_POST["mode"]) {

case 1:
if (isset($_POST["search_key"])) {
    if(!isset($_POST["time"]) && !isset($_POST["language"])) {    // search all
        $results = search_script(["*"], 31, 0, [], true, $_POST["search_key"]);
    }elseif (!isset($_POST["time"])) {    // search in a range of languages
        $language = explode(',', $_POST["language"]);
        $results = search_script(["*"], 31, 0, [], true, $_POST["search_key"], "", $language);
    } else {
        // check if the time is valid
        if (in_array($_POST["time"], $allowed_time)) {
            $date = new DateTime("now");
            $interval = $_POST["time"];
            $date->sub(new DateInterval("P1$interval"));
            $time_field = $date->format("Y/m/d H:i:s");

            if (!isset($_POST["language"])) {    // search in a range of time
                $results = search_script(["*"], 31, 0, [], true, $_POST["search_key"], $time_field);
            } else {    // search in a range of languages ang time
                $language = explode(',', $_POST["language"]);
                $results = search_script(["*"], 31, 0, [], true, $_POST["search_key"], $time_field, $language);
            }
        }
    }
}

break;
case 2:

    $results = search_script(["*"], 31, 0, [], false, '', "");
    break;

}
}


/*
else{
    $results = search_script(["*"], 31, 0, [], false, '', "");
}
*/

if (isset($results)) {
    if ($results == false) {
        $results = [];
    }
    echo json_encode($results);
}