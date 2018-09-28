<?php

class Router
{
    private $module;

    public function __construct($url) {
        // remove GET parameters from request before evaluating
        $pos = strpos($url, "?");
        if ($pos) {
            $url = substr($url, 0, $pos);
        }
        $url = strtolower($url);

        switch ($url)
        {
            case "/":
            case "/home":
                $this->module = "Home";             break;
            case "/editprofile":
                $this->module = "EditProfile";      break;
            case "/profile":
                if (!isset($_GET["id"])) {
                    $this->module = "Profile";
                } else {
                    $this->module = "PublicProfile";
                }
                break;
            case "/4dm1n":
                $this->module = "Admin";            break;
            case "/messenger":
                $this->module = "Messenger";        break;
            case "/robots.txt":
                header("Content-type: text/plain;");
                fpassthru(fopen("robots.txt", "rb"));
                exit(1);
            case "/logout":
                $this->module = "Logout";           break;
            case "/grab-data":
                $this->module = "GrabData";         break;

            // API
            case "/api/friends":
                $this->module = "Friends";          break;
            case "/api/file-upload":
                $this->module = "FileUpload";       break;
            case "/api/posts":
                $this->module = "Posts";            break;
            case "/api/messages":
                $this->module = "Messages";         break;
            case "/api/file":
                $this->module = "File";             break;
            //case "/api/image":
            //    $this->module = "Image";            break;
            default:
                $this->module = "Error404";         break;
        }
    }

    public function getModule() {
        return $this->module;
    }
}