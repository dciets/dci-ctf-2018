<?php
require_once __DIR__.'/../utils.php';

$username = "";
$password = "";
$php_output = "";
$mysql_output = "";

$db  = db_connect(MYSQL_SQL5_USERNAME, MYSQL_SQL5_PASSWORD);
if (isset($_POST["login"])) {
    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);

    try {
        $sql = "SELECT * FROM users5 WHERE username = '$username' and password = '$password'";
        $mysql_output = $db->query($sql)->fetchAll();
        
        if (count($mysql_output) >= 1) {
            $php_output = "Good job, you now have access to Filters 3.";
            set_access(3);
        } else {
            $php_output = "Invalid credentials.";
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