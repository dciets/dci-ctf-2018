<?php
require_once __DIR__.'/../utils.php';

$username = "";
$password = "";
$php_output = "";
$mysql_output = "";

$db  = db_connect(MYSQL_SQL2_USERNAME, MYSQL_SQL2_PASSWORD);
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    try {
        $sql = "SELECT * FROM users2 WHERE username != 'admin' AND (username = '$username' AND password = '$password')";
        $mysql_output = $db->query($sql)->fetchAll();
        
        if (count($mysql_output) >= 1) {
            if ($mysql_output[0]["username"] == "admin") {
                $php_output = "Good job, you now have access to Basics 3.";
                set_access(3);
            } else {
                $php_output = "Successfully logged in, but not as an admin.";
            }
        } else {
            $php_output = "Wrong credentials.";
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
    "success" => $_SESSION["level"] >= 3,
);
echo json_encode($output);
?>