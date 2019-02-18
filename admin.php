<?php
/**
 * admin.php
 *  mode
 *      0 - get feedback
 *          input
 *              user_id
 *          output
 *              unsolved - the feedback that are under review. return empty result if there is no result
 *              solved - the feedback that are solved. return empty result if there is no result
 *      1 - solve a feedback
 *          input
 *              user_id
 *              feedback_id
 *          output
 *              0 - success
 *              1 - unknown error
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('configuration/load.php');

if (isset($_POST["mode"])) {
    switch ($_POST["mode"]) {
        // get feedback
        case 0:
            if (isset($_POST["user_id"])) {
                $results = [];

                // get unsolved feedback
                $feedback = search_feedback(["*"], true, 1, false, [0]);
                if ($feedback == false) {
                    $results["unsolved"] = [];
                } else {
                    $results["unsolved"] = $feedback;
                }

                // get solved feedback
                $feedback = search_feedback(["*"], true, 1, false, [1]);
                if ($feedback == false) {
                    $results["solved"] = [];
                } else {
                    $results["solved"] = $feedback;
                }

                echo json_encode($results);
            }
            break;
        // solve a feedback
        case 1:
            if (isset($_POST["user_id"]) && isset($_POST["feedback_id"])) {
                global $db;

                $update = $db->update("feedback",
                    array(
                        "status" => 1,
                        "responseAdministor" => $_POST["user_id"]
                    ),
                    "feedbackID", $_POST["feedback_id"]);
                if ($update == false) {
                    echo json_encode(1);
                } else {
                    echo json_encode(0);
                }
            }
            break;
    }
}
