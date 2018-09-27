<?php

class MessengerController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();
        $this->init_controllers("logs");
        $data["user"]->check_permission(User::CONNECTED);

        // process request here
        $data["title"] = "Flagbook - Messenger";

        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );
        $friends = APIController::get_friends_data($_SESSION["user_id"]);
        $response = $this->merge_response($response, $friends);

        if (isset($_GET["r"]) && isset($_GET["s"])) {
            if (isset($_POST["submit_message"])) {
                $content = isset($_POST["content"]) ? $_POST["content"] : "";
                APIController::post_message($_SESSION["user_id"], $_GET["r"], $content); 
            }
            $messages = APIController::get_messages($_GET["r"], $_GET["s"]);
            $response = $this->merge_response($response, $messages);
        } elseif (count($friends) > 0) {
            header('Location: /messenger?s='.$_SESSION["user_id"].'&r='.$friends["data"]["friends"][0]["id"]);
            exit(1);
        }

        return $response;
    }
}

