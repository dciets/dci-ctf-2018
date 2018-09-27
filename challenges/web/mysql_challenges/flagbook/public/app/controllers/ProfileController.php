<?php

class ProfileController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();
        $data["user"]->check_permission(User::CONNECTED);

        // process request here
        $data["title"] = "Flagbook - Profile";
        $data = $this->handle_update($data);
        $data = $this->handle_private($data);

        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );
        $posts = APIController::get_posts($_SESSION["user_id"]);
        $friends = APIController::get_friends_data($_SESSION["user_id"]);
        $response = $this->merge_response($response, $posts);
        $response = $this->merge_response($response, $friends);
        return $response;
    }

    public function handle_update($data) {
        if (isset($_POST["update_profile"])) {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $status = isset($_POST["status"]) ? $_POST["status"] : "";
            $location = isset($_POST["location"]) ? $_POST["location"] : "";

            $sql = "UPDATE users SET status = ?, location = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $status, PDO::PARAM_STR);
            $stmt->bindParam(2, $location, PDO::PARAM_STR);
            $stmt->bindParam(3, $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION["status"] = $status;
            $_SESSION["location"] = $location;
        }
        return $data;
    }

    public function handle_private($data) {
        if (isset($_POST["send_info"])) {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $info =  isset($_COOKIE["form-data"]) ? $_COOKIE["form-data"] : "";
            
            $sql = "INSERT INTO private_info (info) VALUES (?)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $info, PDO::PARAM_STR);
            $stmt->execute();

            $this->status = 1;
            $this->messages[] = "Info \$ent. Thank you, this means a lot to us $$$.";
        }
        return $data;
    }
}
