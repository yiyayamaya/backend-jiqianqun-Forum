<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//require_once('configuration/load.php');

$format = "Y/m/d H:i";
$date = DateTime::createFromFormat($format, '2018/12/27 10:04');
echo "Format: $format; " . $date->format('U') . "\n";
echo time();