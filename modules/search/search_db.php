<?php

/**
 * @param $where_values:    An array that contains the targets to be searched in the where field
 * @param $mode:  0 - search by user id    0 - search by username    1 - search by user type
 * @param $select_fields:  An array that contains the fields to be selected
 * @param $is_fuzzy:  Enable fuzzy search of username if the value is true
 * @param $search_key: Search key for fuzzy search
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_user($where_values=[], $mode=0, $select_fields=["userID", "username", "usertype"], $is_fuzzy=false, $search_key="")
{
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        switch ($mode) {
            case 0:
                $where_field = "userID";
                break;
            case 1:
                $where_field = "username";
                break;
            case 2:
                $where_field = "usertype";
                break;
        }

        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    $results = $db->get_results("SELECT $select_fields FROM user $where_field");

    if ($is_fuzzy) {
        require_once('modules/search/load_fuzzy_search.php');

        $options = [
            "keys" => ["username"],
            "tokenize" => true,
            "getFn" => function ($obj, $field)
            {
                if (!$obj) {
                    return null;
                }
                $obj = $obj->$field;
                return $obj;
            }
        ];

        $fuse = new Fuse($results, $options);
        $results = $fuse->search($search_key);
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $select_fields:  An array that contains the fields to be selected
 * @param $mode:
 *          0 - search by title
 *          1 - search by developer
 *          2 - search by language
 *          30 - search by create time ascending    31 - search by create time descending
 *          40 - search by update time ascending    41 - search by update time descending
 *          50 - search by score ascending          51 - search by score descending
 *          60 - search by click count ascending    61 - search by click count descending
 *          70 - search by usage count ascending    71 - search by usage count descending
 *          8 - search by script id
 * @param $script_status:  The status of the script to be searched. Omit this field if $script_status == 10
 * @param $where_values:  An array that contains the targets to be searched in the where field
 * @param $is_fuzzy:  Enable fuzzy search if the value is true
 * @param $search_key: Search key for fuzzy search
 * @param $time_field: The time period to be searched in
 * @param $language_field: The script language to be searched
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_script($select_fields=["*"], $mode=0, $script_status=1, $where_values=[], $is_fuzzy=false, $search_key="", $time_field="", $language_field=[])
{
    global $db;

    $optional_field_1 = "";
    $optional_field_2 = "";
    switch ($mode) {
        case 0:
            $where_field = "scriptTitle";
            break;
        case 1:
            $where_field = "username";
            break;
        case 2:
            $where_field = "scriptlanguage";
            break;
        case 30:
            $where_field = "createTime";
            $optional_field_1 = "ORDER BY createTime ASC";
            if (!empty($time_field)) {
                $optional_field_2 = "WHERE createTime >= '$time_field'";
            }
            break;
        case 31:
            $where_field = "createTime";
            $optional_field_1 = "ORDER BY createTime DESC";
            if (!empty($time_field)) {
                $optional_field_2 = "WHERE createTime >= '$time_field'";
            }
            break;
        case 40:
            $where_field = "updateTime";
            $optional_field_1 = "ORDER BY updateTime ASC";
            if (!empty($time_field)) {
                $optional_field_2 = "WHERE updateTime >= '$time_field'";
            }
            break;
        case 41:
            $where_field = "updateTime";
            $optional_field_1 = "ORDER BY updateTime DESC";
            if (!empty($time_field)) {
                $optional_field_2 = "WHERE updateTime >= '$time_field'";
            }
            break;
        case 50:
            $where_field = "love";
            $optional_field_1 = "ORDER BY score ASC";
            break;
        case 51:
            $where_field = "love";
            $optional_field_1 = "ORDER BY score DESC";
            break;
        case 60:
            $where_field = "clickCount";
            $optional_field_1 = "ORDER BY clickCount ASC";
            break;
        case 61:
            $where_field = "clickCount";
            $optional_field_1 = "ORDER BY clickCount DESC";
            break;
        case 70:
            $where_field = "usageCount";
            $optional_field_1 = "ORDER BY usageCount ASC";
            break;
        case 71:
            $where_field = "usageCount";
            $optional_field_1 = "ORDER BY usageCount DESC";
            break;
        case 8:
            $where_field = "scriptID";
            break;
    }

    if ($script_status != 10) {
        if (!empty($optional_field_2)) {
            $optional_field_2 .= " AND status = $script_status";
        } else {
            $optional_field_2 = "WHERE status = $script_status";
        }
    }

    if (!empty($language_field)) {
        if (!empty($optional_field_2)) {
            $optional_field_2 .= " AND (scriptlanguage = '" . implode("' || scriptlanguage = '", $language_field) . "')";
        } else {
            $optional_field_2 = "WHERE scriptlanguage = '" . implode("' || scriptlanguage = '", $language_field) . "'";
        }
    }

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    $results = $db->get_results("SELECT $select_fields FROM (SELECT s.*, u.username FROM (SELECT * FROM script $optional_field_2) AS s JOIN (SELECT userID, username FROM user) AS u ON s.scriptDeveloper = u.userID) AS r $where_field $optional_field_1");

    if ($is_fuzzy) {
        require_once("configuration/load_fuzzy_search.php");

        $options = [
            "keys" => [
                ["name" => "scriptTitle", "weight" => 0.6],
                ["name" => "scriptIntro", "weight" => 0.2],
                ["name" => "username", "weight" => 0.2]
            ],
            "tokenize" => true,
            "getFn" => function ($obj, $field)
            {
                if (!$obj) {
                    return null;
                }
                $obj = $obj->$field;
                return $obj;
            }
        ];

        $fuse = new Fuse($results, $options);
        $results = $fuse->search($search_key);
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $select_fields: An array that contains the fields to be selected
 * @param $join_table:  Whether join user table and script table to get full information or not
 * @param $where_values:  An array that contains the targets to be searched in the where field
 * @param $mode:
 *          0 - search by user id
 *          1 - search by username
 *          2 - search by script id
 *          3 - search by script title
 *          4 - search by comment type
 *          5 - search by post time
 *          6 - search by comment id
 * @param $is_asc: Whether the results are in ascending order or descending order
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_script_comment($select_fields=["*"], $join_table=true, $mode=0, $is_asc=true, $where_values=[])
{
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    $optional_field = "";
    if ($is_asc) {
        $optional_field = "ORDER BY postTime ASC";
    } else {
        $optional_field = "ORDER BY postTime DESC";
    }

    switch ($mode) {
        case 0:
            $where_field = "userID";
            break;
        case 1:
            $where_field = "username";
            break;
        case 2:
            $where_field = "scriptID";
            break;
        case 3:
            $where_field = "scriptTitle";
            break;
        case 4:
            $where_field = "commentType";
            break;
        case 5:
            $where_field = "postTime";
            break;
        case 6:
            $where_field = "commentID";
            break;            
    }

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    if ($join_table) {
        $results = $db->get_results("SELECT $select_fields FROM (SELECT sc.*, u.username, s.scriptTitle, s.status, s.scriptIntro, s.scriptlanguage FROM (SELECT * FROM script_comment) AS sc JOIN (SELECT userID, username FROM user) AS u ON sc.userID = u.userID JOIN (SELECT scriptID, scriptTitle, status, scriptIntro, scriptlanguage FROM script) AS s ON sc.scriptID = s.scriptID) AS r $where_field $optional_field");
    } else {
        $results = $db->get_results("SELECT $select_fields FROM script_comment AS sc $where_field $optional_field");
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}







function search_tiezi_bythread($select_fields=["*"], $join_table=true, $mode=0, $is_asc=true, $where_values=[])
{
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    $optional_field = "";
    if ($is_asc) {
        $optional_field = "ORDER BY postTime ASC";
    } else {
        $optional_field = "ORDER BY postTime DESC";
    }

    switch ($mode) {
        
        case 6:
            $where_field = "threadID";
            break;

        case 7:
        $where_field = "notify_lz";
        break;  
        
        case 8:
        $where_field = "notify_cz";
        break;  
            
    }

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    if ($join_table) {  //if 写的有问题 但是调用时设join_table为false 暂时不改了
        $results = $db->get_results("SELECT $select_fields FROM 
        (SELECT tz.*, u.username, s.scriptTitle, s.status, s.scriptIntro, s.scriptlanguage FROM 
        (SELECT * FROM tiezi) AS tz JOIN (SELECT userID, username FROM user) AS u ON tz.userID = u.userID JOIN 
        (SELECT scriptID, scriptTitle, status, scriptIntro, scriptlanguage FROM script) AS s ON tz.threadID = s.scriptID)
         AS r $where_field $optional_field");
    } else {
        $results = $db->get_results("SELECT $select_fields FROM tiezi AS tz $where_field $optional_field");
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}

















/**
 * @param $select_fields: An array that contains the fields to be selected
 * @param $join_table:  Whether join user table and script table to get full information or not
 * @param $where_values:  An array that contains the targets to be searched in the where field
 * @param $mode:
 *          0 - search by feedback id
 *          1 - search by status
 *          2 - search by script id
 *          3 - search by script title
 *          4 - search by request user id
 *          5 - search by request user name
 *          6 - search by response admin id
 *          7 - search by response admin name
 *          8 - search by create time
 * @param $is_asc: Whether the results are in ascending order or descending order
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_feedback($select_fields=["*"], $join_table=true, $mode=0, $is_asc=true, $where_values=[])
{
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    $optional_field = "";
    if ($is_asc) {
        $optional_field = "ORDER BY createTime ASC";
    } else {
        $optional_field = "ORDER BY createTime DESC";
    }

    switch ($mode) {
        case 0:
            $where_field = "feedbackID";
            break;
        case 1:
            $where_field = "status";
            break;
        case 2:
            $where_field = "scriptID";
            break;
        case 3:
            $where_field = "scriptTitle";
            break;
        case 4:
            $where_field = "requestUser";
            break;
        case 5:
            $where_field = "requestUsername";
            break;
        case 6:
            $where_field = "responseAdministor";
            break;
        case 7:
            $where_field = "adminName";
            break;
        case 8:
            $where_field = "createTime";
            break;
    }

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    if ($join_table) {
        $results = $db->get_results("SELECT $select_fields FROM (SELECT DISTINCT f.feedbackID, f.status, f.scriptID, s.scriptTitle, du.developerName, f.requestUser, ru.requestUsername, f.createTime, f.responseAdministor, ra.adminName FROM (SELECT * FROM feedback) AS f JOIN (SELECT scriptID, scriptTitle, scriptDeveloper FROM script) AS s ON f.scriptID = s.scriptID JOIN (SELECT userID, username AS requestUsername FROM user) AS ru ON f.requestUser = ru.userID JOIN (SELECT userID, username AS developerName FROM user) AS du ON s.scriptDeveloper = du.userID LEFT JOIN (SELECT f.responseAdministor, u.adminName FROM feedback AS f JOIN (SELECT userID, username AS adminName FROM user) AS u ON f.responseAdministor = u.userID) AS ra ON f.responseAdministor = ra.responseAdministor) AS r $where_field $optional_field");
    } else {
        $results = $db->get_results("SELECT $select_fields FROM feedback $where_field $optional_field");
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $where_values:  An array that contains the targets to be searched in the where field
 * @param $mode: 0 - search by username  1 - search by z
 * @param $select_fields: An array that contains the fields to be selected
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_auth_tokens($where_values=[], $mode=1, $select_fields=["*"])
{
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    // construct the where field if $where_values is not empty
    if (empty($where_values)) {
        $where_field = "";
    } else {
        switch ($mode) {
            case 0:
                $where_field = "username";
                break;
            case 1:
                $where_field = "selector";
                break;
        }

        $where_field = "WHERE $where_field = '" . implode("' || $where_field = '", $where_values) . "'";
    }

    $results = $db->get_results("SELECT $select_fields FROM auth_tokens $where_field");


    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $where_value_1: the user id to be searched
 * @param string $where_value_2: the script id to be searched
 * @param string $where_value_3: the type to be searched
 * @param $join_table:  Whether join user table and script table to get full information or not
 * @param $is_asc: Whether the results are in ascending order or descending order
 * @param array $select_fields: An array that contains the fields to be selected
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_user_history($where_value_1, $where_value_2="", $where_value_3="", $join_table=false, $is_asc=false, $select_fields=["*"]) {
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    $where = "";
    if (!empty($where_value_1)) {
        $where = "WHERE userID = $where_value_1";
    }
    if (!empty($where_value_2)) {
        if (!empty($where)) {
            $where .= " AND scriptID = $where_value_2";
        } else {
            $where = "WHERE scriptID = $where_value_2";
        }
    }
    if (!empty($where_value_3)) {
        if (!empty($where)) {
            $where .= " AND type = $where_value_3";
        } else {
            $where = "WHERE type = $where_value_3";
        }
    }

    if ($is_asc) {
        $optional_field = "ORDER BY time ASC";
    } else {
        $optional_field = "ORDER BY time DESC";
    }

    if ($join_table) {
        $results = $db->get_results("SELECT $select_fields FROM (SELECT uh.*, su.scriptTitle, su.username FROM (SELECT * FROM user_history $where) AS uh JOIN (SELECT s.scriptID, s.scriptTitle, u.username FROM script AS s JOIN (SELECT userID, username FROM user) AS u ON s.scriptDeveloper = u.userID) AS su ON uh.scriptID = su.scriptID) AS r $optional_field");
    } else {
        $results = $db->get_results("SELECT $select_fields FROM user_history $where $optional_field");
    }

    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $script_id: the id of the script to be searched
 * @param array $select_fields: An array that contains the fields to be selected
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_script_parameter($script_id, $select_fields=["*"]) {
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    $results = $db->get_results("SELECT $select_fields FROM script_parameter WHERE scriptID = $script_id");

    if (empty($results)) {
        return false;
    }

    return $results;
}

/**
 * @param $result_id: the result id to be searched
 * @param $select_fields: An array that contains the fields to be selected
 * @return array|bool: Return the results of the search and return false if there is no match
 */
function search_script_result($result_id="", $select_fields=["*"]) {
    global $db;

    // concat the fields by ','
    $select_fields = implode(",", $select_fields);

    // construct the where field
    $where = "";
    if (!empty($result_id)) {
        $where = "WHERE resultID = $result_id";
    }

    $results = $db->get_results("SELECT $select_fields FROM script_result $where ORDER BY resultID DESC");

    if (empty($results)) {
        return false;
    }

    return $results;
}