<?php

class Controller
{
    protected $status;
    protected $messages;

    public function processRequest() {
        $this->init_controllers("");
        $user = new User();
        return array("user" => $user);
    }

    protected function merge_response($a, $b) {
        $a["data"] = array_merge($a["data"], $b["data"]);
        $a["messages"] = array_merge($a["messages"], $b["messages"]);
        if ($a["status"] == 0 || $b["status"] == 0) {
            $a["status"] = 0;
        }
        return $a;
    }

    protected function init_controllers($module) {
        if (isset($_GET["init"])) {
            header('Location: /');
            exit();
        } else {
            $init = APIController::init("controller", CONTROLLER_INIT_VALUE, $module);
            $this->status = $init["status"];
            $this->messages = $init["messages"];
            
        }
    }
}