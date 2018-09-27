<?php

class PublicProfileController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();

        $user = APIController::get_user($_GET["id"]);
        if (!$user) {
            header('Location: /');
            exit();
        }

        if ($user["id"] === $_SESSION["user_id"]) {
            header('location: /profile');
            exit();
        }

        // process request here
        $data["title"] = "Flagbook - Profile";
        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );

        $posts = APIController::get_posts($_GET["id"]);
        $friends = APIController::get_friends_data($_GET["id"]);
        $response = $this->merge_response($response, $posts);
        $response = $this->merge_response($response, $friends);

        $response["data"]["username"] = $user["username"];
        $response["data"]["status"] = $user["status"];
        $response["data"]["is_friend"] = APIController::are_friends($_SESSION["user_id"], $_GET["id"]);
        
        return $response;
    }
}

