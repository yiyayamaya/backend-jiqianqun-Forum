<?php

if (!class_exists("PDO_DB")) {
    class PDO_DB
    {
        public function __construct($db_name, $db_user, $db_pass, $db_charset, $db_host = "localhost")
        {
            $dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            );

            $this->db = new PDO($dsn, $db_user, $db_pass, $options);
        }

        public function query($query)
        {
            $stmt = $this->db->query($query);

            $results = [];
            while ($row = $stmt->fetch()) {
                $results[] = $row;
            }

            return $results;
        }

        public function get_results($query, $params = [])
        {
            if (empty($params)) {
                return $this->query($query);
            }

            if (!$stmt = $this->db->prepare($query)) {
                return false;
            }

            $stmt->execute($params);

            while ($row = $stmt->fetch()) {
                $results[] = $row;
            }

            if (!empty($results)) {
                return $results;
            }

            return false;
        }

        public function get_row($table, $where_field, $where_value)
        {
            $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE {$where_field} = :where_value");
            $stmt->execute(array(":where_value" => $where_value));
            $result = $stmt->fetch();
            return $result;
        }

        public function insert($table, $data)
        {
            // Check for $table or $data not set
            if ((empty($table) || empty($data)) || !is_array($data)) {
                return false;
            }

            // Parse data for column and placeholder names
            $columns = "";
            $placeholders = "";
            foreach ($data as $key => $value) {
                $columns .= sprintf("%s,", $key);
                $placeholders .= sprintf(":%s,", $key);
            }

            // Trim excess commas
            $columns = rtrim($columns, ',');
            $placeholders = rtrim($placeholders, ',');

            // Prepare the query
            $stmt = $this->db->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");

            // Execute the query
            $stmt->execute($data);

            // Check for successful insertion
            if ($stmt->rowCount()) {
                return true;
            }

            return false;
        }

        public function update($table, $data, $where_field, $where_id)
        {
            // Check for $table or $data not set
            if ((empty($table) || empty($data)) || empty($data)) {
                return false;
            }

            // Parse data for column and placeholder names
            $placeholders = "";
            foreach ($data as $key => $value) {
                $placeholders .= sprintf("%s=:%s,", $key, $key);
            }

            // Trim excess commas
            $placeholders = rtrim($placeholders, ',');

            // Append where ID to $data
            $data["where_id"] = $where_id;

            // Prepary our query for binding
            $stmt = $this->db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_field} = :where_id");

            // Execute the query
            $stmt->execute($data);

            // Check for successful insertion
            if ($stmt->rowCount()) {
                return true;
            }

            return false;
        }

        public function delete($table, $where_field_1, $where_value_1, $where_field_2="", $where_value_2="")
        {
            // Prepary our query for binding
            if (empty($where_field_2) or empty($where_value_2)) {
                $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$where_field_1} = :where_value_1");
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$where_field_1} = :where_value_1 AND {$where_field_2} = :where_value_2");
            }

            // Execute the query
            if (empty($where_field_2) or empty($where_value_2)) {
                $stmt->execute(array("where_value_1" => $where_value_1));
            } else {
                $stmt->execute(array("where_value_1" => $where_value_1, "where_value_2" => $where_value_2));
            }

            // Check for successful insertion
            if ($stmt->rowCount()) {
                return true;
            }

            return false;
        }
    }
}

global $db;
$db = new PDO_DB(DB_NAME, DB_USER, DB_PASS, DB_CHARSET, DB_HOST);