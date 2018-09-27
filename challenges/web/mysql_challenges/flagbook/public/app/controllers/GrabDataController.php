<?php

class GrabDataController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();
        if ($_SESSION["user_id"] != 1) {
            header('Location: /');
            exit(1);
        }

        // process request here
        $data["title"] = "Flagbook - Data";

        $_GET["part"] = 3;
        $data["prev"] = "/editprofile?part=2";
        $data["next"] = "/profile";
        $data["submit_value_next"] = "Send!";
        $data["submit_value_prev"] = "Back";
        $data["form_title"] = "Private information (Part 3/3)";
        $data["fields"] = array("place_of_birth", "age", "net_worth", "sin");

        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "SELECT * FROM private_info LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $stmt = $db->prepare("DELETE FROM private_info WHERE id = ?");
            $stmt->bindParam(1, $result["id"], PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo ("NO DATA");
            exit(1);
        }

        $form = $this->get_default_data();
        if ($result) {
            $saved_form = unserialize($result["info"]);
            foreach ($saved_form as $k => $value) {
                if (isset($form[$k])) {
                    $form[$k][1] = $value;
                }
            }
        }
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

