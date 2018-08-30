<?php
session_start();
set_access(1);

define("MYSQL_SQL4_USERNAME", "sqli_4");
define("MYSQL_SQL4_PASSWORD", "qZA485Rt4RTn5Vga");
define("MYSQL_SQL5_USERNAME", "sqli_5");
define("MYSQL_SQL5_PASSWORD", "eM63EH2BVEH2EUf6");
define("MYSQL_SQL6_USERNAME", "sqli_6");
define("MYSQL_SQL6_PASSWORD", "7g8nVRvJjC8nXzkW");

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
        array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode=""')
    );
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