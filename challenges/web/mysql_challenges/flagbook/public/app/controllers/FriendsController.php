<?php

class FriendsController extends Controller
{
    public function __construct() {
        $data = parent::processRequest();
        $data["user"]->check_permission(User::CONNECTED);

        if (isset($_GET["action"]) && isset($_GET["friend_id"])) {
            if ($_GET["action"] === "decline") {
                APIController::decline_friend($_GET["friend_id"]);
            } else if ($_GET["action"] === "accept") {
                APIController::accept_friend($_GET["friend_id"]);
            }
        } 

        // always return all friends and friend_requests data
        $response = APIController::get_friends_data($_SESSION["user_id"]);
        echo json_encode($response);
        if (isset($_GET["redirect"])) {
            header('Location: /');
        }
        exit(1);

    }
}


