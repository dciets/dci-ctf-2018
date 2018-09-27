<?php

class PostsController extends Controller
{
    public function __construct() {
        $data = parent::processRequest();
        $data["user"]->check_permission(User::CONNECTED);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle_post($data);
        } else {
            $this->handle_get($data);
        }
    }

    public function handle_post($data) {
        $response = APIController::submit_post();
        echo json_encode($response);
        exit(1);
    }

    public function handle_get($data) {
        $response = array(
            "status" => false,
            "messages" => array("Not implemented yet.")
        );
        echo json_encode($response);
        exit(1);
    }
}

