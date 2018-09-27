<?php
ini_set('session.cookie_httponly', 1 );
require_once('config.php');
require_once('functions.php');
require_once('autoload.php');

$_SESSION["bank"] = isset($_SESSION["bank"]) ? $_SESSION["bank"] : 100000000000;

$app = new Application($_SERVER['REQUEST_URI']);
$app->execute();