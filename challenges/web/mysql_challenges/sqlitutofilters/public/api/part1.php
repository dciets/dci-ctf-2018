<?php
require_once __DIR__.'/../utils.php';

$username = "";
$password = "";
$php_output = "";
$mysql_output = "";

$db  = db_connect(MYSQL_SQL4_USERNAME, MYSQL_SQL4_PASSWORD);
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $username = strtoupper($username);
    $password = strtoupper($password);

    // filter UNION and SELECT from username
    $username = str_replace(array("UNION", "SELECT"), "", $username);
    try {
        $sql = "SELECT * FROM users4 WHERE username = '$username'";
        $mysql_output = $db->query($sql)->fetchAll();
        
        if (count($mysql_output) >= 1) {
            if (sha1($password) === $mysql_output[0]["password"]) {
                $php_output = "Good job, you now have access to Filters 2.";
                set_access(2);
            } else {
                $php_output = "Invalid password.";
            }
        } else {
            $php_output = "Username doesn't exists.";
        }
    } catch (Exception $e) {
        $mysql_output = $e->getMessage();
    }
}

$output = array(
    "phpout" => $php_output,
    "sqlout" => print_r($mysql_output, true),
    "username" => $username,
    "password" => $password,
    "success" => $_SESSION["level"] >= 2,
);
echo json_encode($output);
?>