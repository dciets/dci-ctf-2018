<?php

function db_connect($user, $pwd) {
    $servername = getenv("MYSQL_HOST"); 
    $dbname = "flagbook_db";

    $db = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pwd, 
                  array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode=""'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check connection
    if (!$db) {
        die("Connection failed: " . $db->connect_error);
    } 

    return $db; 
}

function make_unique_filename($target_dir, $target_file)
{
    $result = $target_dir . $target_file;
    $index = 1;
    while (file_exists($result)) {
        $result = $target_dir . $index . "_" . $target_file;
        $index++;
    }
    return $result;
}

function e($str)
{
    return htmlspecialchars($str);
}