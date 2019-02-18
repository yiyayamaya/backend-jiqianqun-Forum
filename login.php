<?php
/**
 * login.php
 *  input
 *      username
 *      password
 *      cookie
 *
 *  output
 *      login_status
 *          0 - successfully log in
 *          1 - successfully log in with session
 *          2 - empty username
 *          3 - empty password
 *          4 - wrong username or password
 *          5 - token not exist or expire
 *          6 - unknown error
 *      user
 *      cookie
 */

require_once("configuration/load.php");
require_once("modules/class-login.php");


if (!isset($_POST["cookie"])) {
    $login_status = $login->login($_POST);
} else {
    $login_status = $login->verify_session($_POST["cookie"]);
}
echo json_encode($login_status);