<?php
/**
 * run_script.php
 *  input
 *      script_id
 *      language
 *      parameters
 *      user_id    - optional
 *      mode
 *          0 - run immediately
 *          1 - run on a fixed time
 *              time
 *          2 - run repeatedly
 *              iter
 *              interval
 *                  0 - 1 min
 *                  1 - 30 min
 *                  2 - 60 min
 *  output
 *      result - the result of the execution or empty result if an error occurs during the execution
 *      status
 *          0 - success
 *          1 - the parameters contain space
 *          2 - the time is not valid
 *          3 - an error occurs during the execution
 *          4 - unknown error
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('configuration/load.php');


// check if the time is valid
if (isset($_POST["time"])) {
    $time = DateTime::createFromFormat("Y/m/d H:i", $_POST["time"])->format('U');
    if ($time <= time()) {
        echo json_encode(2);
        exit();
    }
} else {
    echo "valid";
}

if (isset($_POST["script_id"]) && isset($_POST["language"]) && isset($_POST["mode"]) && isset($_POST["parameters"])) {
    // check if the parameters contain space
    $parameters = explode('))', $_POST["parameters"]);
    if (end($parameters) == '') {
        array_pop($parameters);
    }
    foreach ($parameters as $parameter) {
        if (strpos($parameter, ' ')) {
            echo json_encode(1);
            return;
        }
    }

    $language = $_POST["language"];
    if ($language == "PHP") {
        $cmd = "php";
        $ext = "php";
    } elseif ($language == "Python") {
        $cmd = "python";
        $ext = "py";
    } elseif ($language == "JavaScript") {
        $cmd = "node";
        $ext = "js";
    } elseif ($language == "Perl") {
        $cmd = "perl";
    } elseif ($language == "Ruby") {
        $cmd = "ruby";
    }

    $script_id = $_POST["script_id"];
    $parameter = implode(" ", $parameters);

    switch ($_POST["mode"]) {
        // run immediately
        case 0:
        // run on a fixed time
        case 1:
            // sleep if in mode 1
            if (isset($_POST["time"])) {
                sleep($time - time());
            }

            $result = shell_exec("$cmd ../scripts/$script_id/main.$ext $parameter");
            if (empty($result)) {
                $result = "An ERROR occurred during the execution";
                echo json_encode(3);
            }

            // usage count count +1
            $script = search_script(["*"], 8, 10, [$_POST["script_id"]])[0];
            $update = $db->update("script",
                array(
                    "usageCount" => $script->usageCount + 1
                ),
                "scriptID", $_POST["script_id"]);

            if ($update) {
                // add to user history amd result tables if log in
                if (isset($_POST["user_id"])) {
                    $history = $db->insert("user_history",
                        array(
                            "userID" => $_POST["user_id"],
                            "scriptID" => $_POST["script_id"],
                            "type" => 0,
                            "time" => date("Y/m/d H:i:s", time()),
                            "parameter" => $parameter
                        )
                    );

                    if ($history) {
                        $history_id = search_user_history($_POST["user_id"], $_POST["script_id"], 0, false)[0]->historyID;
                        $history = $db->insert("script_result",
                            array(
                                "resultID" => $history_id,
                                "userID" => $_POST["user_id"],
                                "scriptID" => $_POST["script_id"],
                                "iterationID" => 0,
                                "result" => $result
                            )
                        );
                        if ($history) {
                            echo json_encode(0);
                        }
                    }
                }
            }
            echo json_encode(4);
            break;
        // run repeatedly
        case 2:
            if (isset($_POST["iter"]) && isset($_POST["interval"])) {
                $iter = $_POST["iter"];
                $counter = 0;
                switch ($_POST["interval"]) {
                    case 0:
                        $interval = 60;
                        break;
                    case 1:
                        $interval = 1800;
                        break;
                    case 2:
                        $interval = 3600;
                        break;
                }
                while ($iter--) {
                    $result = shell_exec("$cmd ../scripts/$script_id/main.$ext $parameter");
                    if (empty($result)) {
                        $result = "An ERROR occurred during the execution";
                        echo json_encode(3);
                    }

                    // usage count count +1
                    $script = search_script(["*"], 8, 10, [$_POST["script_id"]])[0];
                    $update = $db->update("script",
                        array(
                            "usageCount" => $script->usageCount + 1
                        ),
                        "scriptID", $_POST["script_id"]);

                    if ($update) {
                        // add to user history amd result tables if log in
                        if (isset($_POST["user_id"])) {
                            $history = $db->insert("user_history",
                                array(
                                    "userID" => $_POST["user_id"],
                                    "scriptID" => $_POST["script_id"],
                                    "type" => 0,
                                    "time" => date("Y/m/d H:i:s", time()),
                                    "parameter" => $parameter
                                )
                            );

                            if ($history) {
                                $history_id = search_user_history($_POST["user_id"], $_POST["script_id"], 0, false)[0]->historyID;
                                $history = $db->insert("script_result",
                                    array(
                                        "resultID" => $history_id,
                                        "userID" => $_POST["user_id"],
                                        "scriptID" => $_POST["script_id"],
                                        "iterationID" => $counter++,
                                        "result" => $result
                                    )
                                );
                                if ($history) {
                                    echo json_encode(0);
                                    break;
                                }
                            }
                        }
                    }
                    echo json_encode(4);
                    sleep($interval);
                }
            }
            break;
    }
}