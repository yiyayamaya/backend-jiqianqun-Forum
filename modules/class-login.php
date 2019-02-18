<?php

class Login
{
    public $user;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    public function verify_session($cookie)
    {
        $msg = [
            "login_status" => []
        ];
        $cookie = rawurldecode($cookie);
        list($selector, $authenticator) = explode(':', $cookie);

        $auth_token = search_auth_tokens([$selector]);
        if ($auth_token != false) {
            $auth_token = $auth_token[0];
            if (hash_equals($auth_token->token, hash("sha256", base64_decode($authenticator)))) {
                $current_time = date("Y-m-d\TH:i:s", time());
                if ($current_time < $auth_token->expires) {
                    $user = $this->user_exists($auth_token->userID);
                    if ($user != false) {
                        $msg["login_status"][] = 1;
                        $msg["user"] = $user;
                        return $msg;
                    } else {
                        $msg["login_status"][] = 6;
                        return $msg;
                    }
                }
            }
            // Delete the invalid token
            $this->db->delete("auth_tokens", "selector", $selector);
        }
        $msg["login_status"][] = 5;
        return $msg;
    }

    public function login($post)
    {
        $msg = [
            "login_status" => []
        ];

        // Check if required fields are all completed
        $required = array("username", "password");

        foreach ($required as $idx => $key) {
            if (empty($post[$key])) {
                $msg["login_status"][] = $idx + 2;
            }
        }

        if (!empty($msg["login_status"])) {
            return $msg;
        }

        // Check if user exists
        $user = $this->user_exists($post["username"], 1, true);

        if ($user != false) {
            if (password_verify($post["password"], $user->password)) {
                $cookie = $this->create_cookie($user);
                if ($cookie != false) {
                    $msg["login_status"][] = 0;
                    unset($user->password);
                    $msg["user"] = $user;
                    $msg["cookie"] = $cookie;
                    return $msg;
                } else {
                    $msg["login_status"][] = 6;
                }
            }
        }
        $msg["login_status"][] = 4;
        return $msg;
    }

    public function register($post)
    {
        $register_status = [];

        // Check if required fields are all completed
        $required = array("username", "password", "cfpassword");

        foreach ($required as $idx => $key) {
            if (empty($post[$key])) {
                $register_status[] = $idx + 1;
            }
        }
        if (!empty($register_status)) {
            return $register_status;
        }

        if ($post["password"] != $post["cfpassword"]) {
            return [4];
        }

        // Check if username exists already
        if (false !== $this->user_exists($post["username"], 1)) {
            return [5];
        }

        // Create if doesn't exist
        $insert = $this->db->insert("user",
            array(
                "username" => $post["username"],
                "password" => password_hash($post["password"], PASSWORD_DEFAULT)
            )
        );

        // Create user folder
        $folder = $this->create_folder($post["username"]);

        if ($insert == true and $folder == true) {
            return [0];
        }

        return [6];
    }

    private function create_cookie($user)
    {
        // Delete old tokens
        $this->db->delete("auth_tokens", "userID", $user->userID);

        // Create selector and authenticator
        $selector = base64_encode(random_bytes(9));
        $authenticator = random_bytes(33);

        $cookie = rawurlencode($selector . ':' . base64_encode($authenticator));

        // Insert auth token into database
        $insert = $this->db->insert("auth_tokens",
            array(
                "userID" => $user->userID,
                "selector" => $selector,
                "token" => hash("sha256", $authenticator),
                "expires" => date("Y-m-d\TH:i:s", time() + 86400)
            )
        );
        if ($insert) {
            return $cookie;
        } else {
            return false;
        }
    }

    private function user_exists($where_values, $mode=0, $get_password=false)
    {
        if ($get_password) {
            $user = search_user([$where_values], $mode, ["userID", "username", "password","usertype"]);
        } else {
            $user = search_user([$where_values], $mode);
        }

        if ($user != false) {
            return $user[0];
        }

        return false;
    }

    private function create_folder($username)
    {
        $result = search_user([$username], 1, ["userID"])[0];
        $user_id = $result->userID;
        $folder_path = "../users/$user_id";

        // create main folder
        $make_folder = mkdir($folder_path, 0750);
        if (!$make_folder) {
            return false;
        }

        // create images folder
        $make_folder = mkdir($folder_path . "/images", 0750);
        if (!$make_folder) {
            return false;
        }

        return true;
    }
}

$login = new Login;