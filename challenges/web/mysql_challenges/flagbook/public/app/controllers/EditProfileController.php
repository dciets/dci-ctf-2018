<?php

class EditProfileController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();
        $data["user"]->check_permission(User::CONNECTED);
        $form = $this->get_default_data();

        if (isset($_COOKIE["form-data"])) {
            $saved_form = unserialize($_COOKIE["form-data"]);
            foreach ($saved_form as $k => $value) {
                if (isset($form[$k])) {
                    $form[$k][1] = $value;
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST as $k => $val) {
                if (isset($form[$k])) {
                    $form[$k][1] = e($val);
                }
            }

            $save_form = array();
            foreach ($form as $k => $val) {
                $save_form[$k] = $val[1];
            }
            setcookie("form-data", serialize($save_form), time() + (86400 * 30), "/"); // 86400 = 1 day
        } 

        if ($_GET["part"] == 1) {
            $data["prev"] = "";
            $data["next"] = "/editprofile?part=2";
            $data["submit_value_next"] = "Next";
            $data["submit_value_prev"] = "";
            $data["form_title"] = "Private information (Part 1/3)";
            $data["fields"] = array("place_of_birth", "age");
        } elseif ($_GET["part"] == 2) {
            $data["prev"] = "/editprofile?part=1";
            $data["next"] = "/editprofile?part=3";
            $data["submit_value_next"] = "Next";
            $data["submit_value_prev"] = "Back";
            $data["form_title"] = "Private information (Part 2/3)";
            $data["fields"] = array("net_worth", "sin");
        } elseif ($_GET["part"] == 3) {
            $data["prev"] = "/editprofile?part=2";
            $data["next"] = "/profile";
            $data["submit_value_next"] = "Send!";
            $data["submit_value_prev"] = "Back";
            $data["form_title"] = "Private information (Part 3/3)";
            $data["fields"] = array("place_of_birth", "age", "net_worth", "sin");
        } else {
            header('Location: /editprofile?part=1');
            exit(1);
        }

        $data["title"] = "Flagbook - profile";
        $data["form"] = $form;

        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );
        return $response;
    }

    public function get_default_data() {
        return array(
            "place_of_birth" => array("Where were you born?", ""),
            "age" => array("How old are you?", ""),
            "net_worth" => array("What is your net worth in \$USD?", ""),
            "sin" => array("What is your SIN?", "")
        );
    }
}