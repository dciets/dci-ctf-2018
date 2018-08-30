<?php
session_start();
set_access(1);

define("MYSQL_SQL1_USERNAME", "sqli_1");
define("MYSQL_SQL1_PASSWORD", "Pr365KcH8nuWsHfU");
define("MYSQL_SQL2_USERNAME", "sqli_2");
define("MYSQL_SQL2_PASSWORD", "nF766UNjt9UZ7way");
define("MYSQL_SQL3_USERNAME", "sqli_3");
define("MYSQL_SQL3_PASSWORD", "9UD4eryuy4B7fD2W");

function get_sources($filename, $from, $to) {
    $handle = fopen($filename, "r");
    $result = "";
    $line_index = 1;
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            if ($line_index >= $from && $line_index <= $to) {
                $result .= $line;
            }
            $line_index++;
        }
        fclose($handle);
    } else {
        $result = "Error opening file.";
    }
    return $result;
}

function db_connect($user, $pwd) {
    $servername = getenv("MYSQL_HOST");
    $dbname = "sqli";
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pwd, 
                  array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode=""'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Check connection
    if (!$db) {
        die("Connection failed: " . $db->connect_error);
    } 
    return $db; 
}

function set_access($level) {
    if (!isset($_SESSION["level"])) {
        $_SESSION["level"] = 1;
    }
    if ($_SESSION["level"] < $level) {
        $_SESSION["level"] = $level;
    }
}