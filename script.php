<?php
/**
 * script.php
 *  mode
 *      0 - get the information of the script
 *          input
 *              script_id
 *              user_id    - optional
 *          output
 *              script      - information of the script
 *              comments    - comments of the script
 *              is_scored
 *                  0 - the user has not given a score to the script
 *                  1 - the user has given a score to the script
 *              is_loved
 *                  0 - the user has not loved the script
 *                  1 - the user has loved the script
 *              return empty result if an error occurs
 *
 *      1 - create new script
 *          input
 *              user_id
 *              title
 *              language
 *              intro
 *              parameter_name
 *              parameter_intro
 *              parameters
 *              upload_file
 *          output
 *              script_id - return if success
 *              status
 *                  0 - successfully create the script
 *                  1 - the extension of the file is not allowed
 *                  2 - the file is empty
 *                  3 - the size of the file is too large
 *                  4 - file name contains illegal characters
 *                  5 - file name contains more than 250 characters
 *                  6 - the script fails to pass the test
 *                  7 - the test parameter contains spacex
 *                  8 - unknown error
 *
 *      2 - create new comment
 *          input
 *              user_id
 *              title
 *              intro
 *          output
 *              username
 *              time
 *              return empty result if an error occurs
 *
 *      3 - score a script
 *          input
 *              user_id
 *              script_id
 *              field
 *                  0 - goodCount
 *                  1 - okCount
 *                  2 - badCount
 *          output
 *              updated scores
 *              return empty result if an error occurs
 *
 *      4 - give a feedback
 *          input
 *              user_id
 *              script_id
 *          output
 *              0 - successfully give a feedback
 *              1 - unknown error
 *
 *      5 - love a script
 *          input
 *              user_id
 *              script_id
 *          output
 *
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once("configuration/load.php");


if (isset($_POST["mode"])) {
    global $db;

    switch ($_POST["mode"]) {
        // get the information of the script
        case 0:
            if (isset($_POST["script_id"])) {
                $result = [];
                $script = search_script(["*"], 8, 10, [$_POST["script_id"]]);
                if ($script != false) {
                    $script = $script[0];
                    $result["script"] = $script;

                    // check if there is any comment of the script
                    $comments = search_script_comment(["*"], true, 2, false, [$_POST["script_id"]]);
                    if ($comments != false) {
                        $result["comments"] = $comments;
                    } else {
                        $result["comments"] = [];
                    }


                    /*
                    // get the parameters of the script
                    $parameters = search_script_parameter($_POST["script_id"], ["name, description"]);
                    if ($parameters == false) {
                        echo json_encode("");
                        break;
                    } else {
                        $result["parameters"] = $parameters;
                    }
                    */

                    // click count + 1
                    $update = $db->update("script",
                        array(
                            "clickCount" => $script->clickCount + 1
                        ),
                        "scriptID", $_POST["script_id"]);
                    if ($update == false) {
                        echo json_encode("");
                        break;
                    }

                    // check if user has given a score
                    if (isset($_POST["user_id"])) {
                        $is_scored = search_user_history($_POST["user_id"], $_POST["script_id"], 1);
                        if ($is_scored == false) {
                            $result["is_scored"] = 0;
                        } else {
                            $result["is_scored"] = 1;
                        }

                        $is_loved = search_user_history($_POST["user_id"], $_POST["script_id"], 2);
                        if ($is_loved == false) {
                            $result["is_loved"] = 0;
                        } else {
                            $result["is_loved"] = 1;
                        }
                    }
                }


                else{

                    $result=777;

                }
                echo json_encode($result);
            }
            break;
        // create new script 创建新贴吧
        case 1:
            if (isset($_POST["user_id"]) && isset($_POST["title"]) && isset($_POST["language"]) && isset($_POST["intro"]) && isset($_POST["parameter_name"]) && isset($_POST["parameter_intro"]) && isset($_POST["parameters"]) ) {
                require_once("modules/upload.php");
                $result = [];
                $status = "";
                $parameters = explode('))', $_POST["parameters"]);
                if (end($parameters) == '') {
                    array_pop($parameters);
                }
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, ' ')) {
                        $status = 7;
                        break;
                    }
                }
                if (!empty($status)) {
                    $result["status"] = $status;
                    echo json_encode($result);
                    break;
                }

                // add to script table
                $time = date("Y/m/d H:i:s", time());
                $insert = $db->insert("script",
                    array(
                        "scriptTitle" => $_POST["title"],
                        "scriptDeveloper" => $_POST["user_id"],
                        "scriptIntro" => $_POST["intro"],
                        "scriptlanguage" => $_POST["language"],
                        "createTime" => $time,
                        "version" =>  ''
                    )
                );
                if ($insert) {
                        $script_id = search_script(["scriptID"], 31, 10, [$time])[0]->scriptID;
                   
                        $result["status"] = 0;
                        $result["script_id"] = $script_id;
                        echo json_encode($result);
                        break;
                    
                   
                   
                   
                } else {
                    $result["status"] = 8;
                    echo json_encode($result);
                    break;
                }
            }
            break;
        // create new comment 创建新主题
        case 2:
            if (isset($_POST["user_id"]) && isset($_POST["script_id"]) && isset($_POST["comment"])) {
                $results = [];
                $time = date("Y/m/d H:i:s", time());
                $insert = $db->insert("script_comment",
                    array(
                        "userID" => $_POST["user_id"],
                        "scriptID" => $_POST["script_id"],
                        "thread_content" => $_POST["thread_content"],
                        "commentType" => 0,
                        "userComment" => $_POST["comment"],
                        "postTime" => $time
                    )
                );

                if ($insert) {
                    $thequery="SELECT LAST_INSERT_ID()";
                    $thread_id=$db->query($thequery);

                    //上面两行执行一个查询  刚刚insert到script_comment表里那行的自增id  为了发表主题后不刷新超链接能得到他的id

                    $user = search_user([$_POST["user_id"]], 0, ["username"])[0];
                    $results["username"] = $user->username;
                    $results["time"] = $time;
                    $results["thread_id"] = $thread_id;
                }
                echo json_encode($results);
            }
            break;
        // score a script
        case 3:
            if (isset($_POST["user_id"]) && isset($_POST["script_id"]) && isset($_POST["field"])) {
                $script = search_script(["goodCount", "okCount", "badCount"], 8, 10, [$_POST["script_id"]])[0];

                // get the current score
                switch ($_POST["field"]) {
                    case 0:
                        $field = "goodCount";
                        break;
                    case 1:
                        $field = "okCount";
                        break;
                    case 2:
                        $field = "badCount";
                        break;
                }

                // update the score
                $score = $script->goodCount + 1;
                $update = $db->update("script",
                    array(
                        $field => $score
                    ),
                    "scriptID", $_POST["script_id"]);

                // add to user history
                $insert = $db->insert("user_history",
                    array(
                        "userID" => $_POST["user_id"],
                        "scriptID" => $_POST["script_id"],
                        "type" => 1,
                        "time" => date("Y/m/d H:i:s", time())
                    )
                );

                if ($update == true && $insert == true) {
                    echo json_encode($score);
                } else {
                    echo json_encode("");
                }
            }
//                $is_scored = search_user_history($_POST["user_id"], $_POST["script_id"], 1);
//                if ($is_scored == false) {
//                    $score = $script->score + 1;
//                }
//                else {
//                    if (count($is_scored) % 2 == 0) {
//                        $score = $script->score + 1;
//                    } else {
//                        $score = $script->score - 1;
//                    }
//                }
//
//                // update the score
//                $update = $db->update("script",
//                    array(
//                        "score" => $score
//                    ),
//                    "scriptID", $_POST["script_id"]);
//
//                // add to user history or update user history
//                if ($is_scored == true) {
//                    $delete = $db->delete("user_history", "userID", $_POST["user_id"], "scriptID", $_POST["script_id"]);
//                    if ($delete == false) {
//                        $history = false;
//                    } else {
//                        $history = true;
//                    }
//                } else {
//                    $insert = $db->insert("user_history",
//                        array(
//                            "userID" => $_POST["user_id"],
//                            "scriptID" => $_POST["script_id"],
//                            "type" => 1,
//                            "time" => date("Y/m/d H:i:s", time())
//                        )
//                    );
//                    if ($insert == false) {
//                        $history = false;
//                    } else {
//                        $history = true;
//                    }
//                }
//
//                if ($update == true && $history == true) {
//                    echo json_encode($score);
//                } else {
//                    echo json_encode("");
//                }
//            }
            break;
        case 4:    // give a feedback
            if (isset($_POST["user_id"]) && isset($_POST["script_id"])) {
                $insert = $db->insert("feedback",
                    array(
                        "scriptID" => $_POST["script_id"],
                        "requestUser" => $_POST["user_id"],
                        "createTime" => date("Y/m/d H:i:s", time())
                    )
                );
                if ($insert) {
                    echo json_encode(0);
                } else {
                    echo json_encode(1);
                }
            }
            break;
        // love a script
        case 5:
            if (isset($_POST["user_id"]) && isset($_POST["script_id"])) {
                $script = search_script(["love"], 8, 10, [$_POST["script_id"]])[0];
                $love = $script->love + 1;
                $update = $db->update("script",
                    array(
                        "love" => $love
                    ),
                    "scriptID", $_POST["script_id"]);

                // add to user history
                $insert = $db->insert("user_history",
                    array(
                        "userID" => $_POST["user_id"],
                        "scriptID" => $_POST["script_id"],
                        "type" => 2,
                        "time" => date("Y/m/d H:i:s", time())
                    )
                );

                if ($update == true && $insert == true) {
                    echo json_encode($love);
                } else {
                    echo json_encode("");
                }
            }
            break;
    }
}