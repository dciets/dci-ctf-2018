<?php

class User
{
    private $id;
    private $permission;
    private $name;
    private $about;

    const NO_USER          = 0;
    const USER             = 1;
    const MODERATOR        = 2;
    const ADMIN            = 4;
    const CONNECTED        = array(1,2,3,4);

    public function __construct() {
        $this->id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : -1;
        $this->permission = isset($_SESSION['permission']) ? $_SESSION['permission'] : self::NO_USER;
        $this->name = isset($_SESSION['username']) ? $_SESSION['username'] : "";
        $_SESSION['status'] = isset($_SESSION['status']) ? $_SESSION['status'] : "";
        $_SESSION['location'] = isset($_SESSION['location']) ? $_SESSION['location'] : "";
    }

    public function check_permission($permissions) {
        if ($this->hasPermission($permissions)) {
            return;
        }
        header('Location: /home');
        exit(1);
    }

    public function hasPermission($permissions) {
        foreach ($permissions as $perm) {
            if ($perm == $this->permission) {
                return true;
            }
        }
        return false;
    }
}

