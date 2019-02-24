<?php
header("ACCESS-CONTROL-ALLOW-ORIGIN:http://www.sornhub.com");
/**
 * register.php
 *  input
 *      username
 *      password
 *      cfpassword
 *  output
 *      register_status
 *          0 - successfully register
 *          1 - empty username
 *          2 - empty password
 *          3 - empty confirm password
 *          4 - the confirm password is not consistent with the former one
 *          5 - the username already exists
 *          6 - unknown error
 */

require_once("configuration/load.php");
require_once("modules/class-login.php");


$register_status = $login->register($_POST);
echo json_encode($register_status);