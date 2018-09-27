<?php

class LogoutController
{
    public function __construct() {
        session_unset();
        session_destroy();
        header('Location: /');
        exit();
    }
}