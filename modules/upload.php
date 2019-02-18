<?php

// check if the file name contains only English characters, numbers and (_-.) symbols
function check_file_name($file_name)
{
    return (bool) ((preg_match("`^[-0-9A-Z_\.]+$`i",$file_name)) ? true : false);
}

// check if the file name contains more than 250 characters
function check_file_length ($filename)
{
    return (bool) ((mb_strlen($filename,"UTF-8") > 225) ? true : false);
}

// upload an icon
function upload_icon($user_id, $icon_file)
{
    $allowed_exts = ["jpeg", "jpg", "png"];
    $max_size = 10485760;

    $file_name = $icon_file["name"];
    $tmp = explode('.', $file_name);
    $file_ext = strtolower(end($tmp));

    // check if the file is empty
    if ($icon_file["size"] == 0) {
        return 2;
    }

    // check if the type of the file is not allowed
    if (in_array($file_ext, $allowed_exts) == false) {
        return 1;
    }

    // return false if the size of the icon file is larger than 10MB
    if ($icon_file["size"] > $max_size) {
        return 3;
    }

    if (check_file_name($file_name) == false) {
        return 4;
    }

    if (check_file_length($file_name) == true) {
        return 5;
    }

    $upload_path = "../users/$user_id/images/icon." . $file_ext;
    if (file_exists($upload_path) == true) {
        if (unlink($upload_path) == false) {
            return 7;
        }
    }

    $upload_file = move_uploaded_file($icon_file["tmp_name"], $upload_path);
    if ($upload_file == true) {
        return 0;
    } else {
        return 7;
    }
}

function upload_script($script_id, $script_file, $language, $parameters)
{
    $allowed_exts = ["php", "zip", "pl", "py", "rb", "js"];
    $max_size = 104857600;

    $file_name = $script_file["name"];
    $tmp = explode('.', $file_name);
    $file_ext = strtolower(end($tmp));

    // check if the file is empty
    if ($script_file["size"] == 0) {
        return 2;
    }

    // check if the type of the file is not allowed
    if (in_array($file_ext, $allowed_exts) == false) {
        return 1;
    }

    // return false if the size of the icon file is larger than 10MB
    if ($script_file["size"] > $max_size) {
        return 3;
    }

    if (check_file_name($file_name) == false) {
        return 4;
    }

    if (check_file_length($file_name) == true) {
        return 5;
    }

    $folder_path = "../scripts/$script_id/";

    // delete the folder if it already exists
    if (is_dir($folder_path) == true) {
        $it = new RecursiveDirectoryIterator($folder_path, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        if (rmdir($folder_path) == false) {
            return 8;
        } else {
            // update the update time of the script
            global $db;
            $update = $db->update("script",
                array(
                    "updateTime" => date("Y/m/d H:i:s", time())
                ),
                "scriptID", $script_id);
            if ($update == false) {
                return 8;
            }
        }
    }

    if (mkdir($folder_path, 0750) == false) {
        return 8;
    }

    $upload_path = $folder_path . "main." . $file_ext;
    $upload_file = move_uploaded_file($script_file["tmp_name"], $upload_path);
    if ($upload_file == true) {
        chmod($upload_path, 0750);
        if ($file_ext == "zip") {
            $zip = new ZipArchive;
            if ($zip->open($upload_path) === false) {
                return 8;
            } else {
                $zip->extractTo($folder_path);
                $zip->close();
                if (unlink($upload_path) == false) {
                    return 8;
                }
                // set the permissions
                $it = new RecursiveDirectoryIterator($folder_path, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach($files as $file) {
                    chmod($file->getRealPath(), 0750);
                    chown($file->getRealPath(), "apache");
                }
            }
        }

        // test the script
        if ($language == "PHP") {
            $cmd = "php";
            $ext = "php";
        } elseif ($language == "Python") {
            $cmd = "python";
            $ext = "py";
        } elseif ($language == "JavaScript") {
            $cmd = "phantomjs";
            $ext = "js";
        } elseif ($language == "Perl") {
            $cmd = "perl";
            $ext = "pl";
        } elseif ($language == "Ruby") {
            $cmd = "ruby";
            $ext = "rb";
        }

        $parameter = implode(" ", $parameters);
        $script_path = $folder_path . "main.$ext";
        $result = shell_exec("$cmd $script_path $parameter");
        if (!empty($result)) {
            return 0;
        } else {
            return 6;
        }
    }

    return 8;
}