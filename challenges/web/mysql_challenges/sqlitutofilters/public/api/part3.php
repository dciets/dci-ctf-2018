<?php
require_once __DIR__.'/../utils.php';

$id = 1;
$mysql_output = "";
$php_output = "";
$db  = db_connect(MYSQL_SQL6_USERNAME, MYSQL_SQL6_PASSWORD);
$sent_id = isset($_POST["id"]) ? $_POST["id"] : $id;

if (isset($_POST["id"])) {
    // remove whitespace
    $id = preg_replace('/\s+/', '', $_POST["id"]);
    // remove other banned characters
    $banned = array("'", '"', "#", "-", "*", "/", "=", "<", ">", "&", "|");
    $id = str_replace($banned, "", $id);

    try {
        $sql = "SELECT * FROM users6 WHERE id = $id LIMIT 1"; 
        $mysql_output = $db->query($sql)->fetch();
        if ($mysql_output) {
            $php_output = "User found: " . $mysql_output["username"] . ".";
        } else {
            $php_output = "No user matches this id.";
        }
    } catch (Exception $e) {
        $mysql_output = $e->getMessage();
    }
}

$output = array(
    "sqlout" => print_r($mysql_output, true),
    "phpout" => $php_output,
    "id" => $sent_id,
);
echo json_encode($output);
?>