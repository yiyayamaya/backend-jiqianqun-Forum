<?php
/**
 * edit.php
 *  input
 *      mode
 *          0 - get the information of the script
 *              input
 *                  script_id
 *              output
 *                  script_name
 *                  description
 *                  language
 *                  parameters
 *                  version
 *                  directories - list files and directories in the project
 *                      name - the name of the directory or file
 *                      parent - the id of the parent directory
 *                      path - the relative path of the directory or file
 *                      is_dir
 *                          1 - directory
 *                          2 - file
 *          1 - get content of a file
 *              input
 *                  path
 *              output
 *                  content - the content of the file
 *
 *          2 - modify script information
 *              input
 *                  script_id
 *                  title
 *                  description
 *                  language
 *                  version
 *                  parameter_name
 *                  parameter_intro
 *                  parameters
 *              output
 *                  0 - success
 *                  1 - the script fails to pass the test
 *                  2 - the test parameter contains space
 *                  3 - unknown error
 *
 *          3 - modify a file
 *              input
 *                  script_id
 *                  language
 *                  parameters
 *                  path
 *                  content
 *              output
 *                  0 - success
 *                  1 - the script fails to pass the test
 *                  2 - the test parameter contains space
 *                  3 - unknown error
 *
 *          4 - delete a file
 *              input
 *                  script_id
 *                  path
 *              output
 *                  0 - success
 *                  1 - unknown error
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('configuration/load.php');

if (isset($_POST["mode"])) {
    switch ($_POST["mode"]) {
        // get the information of the script
        case 0:
            if (isset($_POST["script_id"])) {
                $results = [];
                $script_id = $_POST["script_id"];

                // get the name and parameters of the project
                $script = search_script(["scriptTitle", "scriptIntro", "scriptlanguage", "version"], 8, 10, [$script_id])[0];
                $results["script_name"] = $script->scriptTitle;
                $results["description"] = $script->scriptIntro;
                $results["language"] = $script->scriptlanguage;
                $results["version"] = $script->version;
                $results["parameters"] = search_script_parameter($_POST["script_id"], ["name", "description"]);

                // list files and directories inside a specified path
                $folder_path = "../scripts/$script_id";
                $directories = [];
                $dir_id = [];    // the id of the directory or file
                $counter = -1;
                $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder_path, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
                );

                // iterate the directory
                foreach ($iter as $directory) {
                    $dir = $directory->getPath();    // the path of the parent directory
                    // the parent directory already has an id
                    if (isset($dir_id[$dir])) {
                        $dir_id[$directory->getPathname()] = ++$counter;    // assign an id to the directory or file
                        $directories[$counter]["name"] = $directory->getFilename();
                        $directories[$counter]["parent"] = $dir_id[$dir];
                        $directories[$counter]["path"] = $directory->getPathname();
                        // check if it is a directory or file
                        if ($directory->isDir()) {
                            $directories[$counter]["is_dir"] = 1;
                        } else {
                            $directories[$counter]["is_dir"] = 2;
                        }
                    // the parent directory does not have an id
                    } else {
                        $dir_id[$dir] = ++$counter;    // assign an id to the parent directory
                        $directories[$counter]["name"] = basename($dir);
                        $directories[$counter]["is_dir"] = 1;
                        $directories[$counter]["path"] = $dir;
                        $dir_id[$directory->getPathname()] = ++$counter;    // assign an id to the directory or file
                        $directories[$counter]["name"] = $directory->getBasename();
                        $directories[$counter]["parent"] = $dir_id[$dir];
                        $directories[$counter]["is_dir"] = 2;
                        $directories[$counter]["path"] = $directory->getPathname();
                    }
                }
                $results["directories"] = $directories;

                echo json_encode($results);
            }
            break;
        // get content of a file
        case 1:
            if (isset($_POST["path"])) {
                $content = file_get_contents($_POST["path"]);
                echo $content;
            }
            break;
        // modify script information
        case 2:
            if (isset($_POST["script_id"]) && isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["language"]) && isset($_POST["version"]) && isset($_POST["parameter_name"]) && isset($_POST["parameter_intro"]) && isset($_POST["parameters"])) {
                global $db;

                // check if the parameters contain space
                $space = false;
                $parameters = explode('))', $_POST["parameters"]);
                if (end($parameters) == '') {
                    array_pop($parameters);
                }
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, ' ')) {
                        $space = true;
                        break;
                    }
                }
                if ($space) {
                    echo json_encode(2);
                    break;
                }

                $script_id = $_POST["script_id"];

                // update script title and description
                $update = $db->update("script",
                    array(
                        "scriptTitle" => $_POST["title"],
                        "scriptIntro" => $_POST["description"],
                        "version" => $_POST["version"],
                        "updateTime" => date("Y/m/d H:i:s", time())
                    ),
                    "scriptID", $script_id);
                if ($update == false) {
                    echo json_encode(3);
                    break;
                } else {
                    // delete the old parameters
                    $delete = $db->delete("script_parameter", "scriptID", $script_id);
                    if ($delete == false) {
                        echo json_encode(3);
                        break;
                    } else {
                        // insert new parameters
                        $parameter_name = explode('))', $_POST["parameter_name"]);
                        if (end($parameter_name) == '') {
                            array_pop($parameter_name);
                        }
                        $parameter_intro = explode('))', $_POST["parameter_intro"]);
                        if (end($parameter_intro) == '') {
                            array_pop($parameter_intro);
                        }
                        foreach ($parameter_name as $idx => $name) {
                            $insert = $db->insert("script_parameter",
                                array(
                                    "scriptID" => $script_id,
                                    "name" => $name,
                                    "description" => $parameter_intro[$idx]
                                )
                            );
                        }

                        // test the script
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
                        $parameter = implode(" ", $parameters);
                        $script_path = "../scripts/$script_id/main.$ext";
                        $result = shell_exec("$cmd $script_path $parameter");
                        if (!empty($result)) {
                            echo json_encode(0);
                        } else {
                            echo json_encode(1);
                        }
                    }
                }
            }
            break;
        // modify a file
        case 3:
            if (isset($_POST["script_id"]) && isset($_POST["language"]) && isset($_POST["parameters"]) && isset($_POST["path"]) && isset($_POST["content"])) {
                global $db;

                // check if the parameters contain space
                $space = false;
                $parameters = explode('))', $_POST["parameters"]);
                if (end($parameters) == '') {
                    array_pop($parameters);
                }
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, ' ')) {
                        $space = true;
                        break;
                    }
                }
                if ($space) {
                    echo json_encode(2);
                    break;
                }

                // update the update time
                $update = $db->update("script",
                    array(
                        "updateTime" => date("Y/m/d H:i:s", time())
                    ),
                    "scriptID", $_POST["script_id"]);
                if ($update == false) {
                    echo json_encode(3);
                    break;
                } else {
                    // delete the old file
                    $path = $_POST["path"];
                    $delete = unlink($path);

                    // create a new file
                    if ($delete) {
                        if ($handle = fopen($_POST["path"], "w+")) {
                            if (fwrite($handle, $_POST["content"])) {
                                // test the script
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
                                $parameter = implode(" ", $parameters);
                                $result = shell_exec("$cmd $path $parameter");
                                if (!empty($result)) {
                                    echo json_encode(0);
                                    break;
                                } else {
                                    echo json_encode(1);
                                    break;
                                }
                            }
                        }
                    }
                }
                echo json_encode(3);
            }
            break;
        // delete a file
        case 4:
            if (isset($_POST["script_id"]) && isset($_POST["path"])) {
                // delete the file
                $delete = unlink($_POST["path"]);
                if ($delete) {
                    // update the update time
                    $update = $db->update("script",
                        array(
                            "updateTime" => date("Y/m/d H:i:s", time())
                        ),
                        "scriptID", $_POST["script_id"]);
                    if ($update) {
                        echo json_encode(0);
                        break;
                    }
                }
                echo json_encode(1);
            }
            break;
    }
}