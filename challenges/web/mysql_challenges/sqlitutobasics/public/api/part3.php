<?php
require_once __DIR__.'/../utils.php';

$username = "";
$password = "";
$php_output = "";
$mysql_output = "";

$db  = db_connect(MYSQL_SQL3_USERNAME, MYSQL_SQL3_PASSWORD);
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    try {
        $sql = "SELECT * FROM users3 WHERE username = '$username'";
        $mysql_output = $db->query($sql)->fetchAll();
        
        if (count($mysql_output) >= 1) {
            if (sha1($password) === $mysql_output[0]["password"]) {
                $php_output = "Good job, here is the flag: <censored>";
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

if (substr($php_output, 0, 4) === "Good") {
    $php_output = "Good job, here is the flag: DCI{SQLi_baby_first_steps}";
}

$output = array(
    "phpout" => $php_output,
    "sqlout" => print_r($mysql_output, true),
    "username" => $username,
    "password" => $password,
);
echo json_encode($output);
?>