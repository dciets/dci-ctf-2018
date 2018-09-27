<?php

class AdminController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();

        // process request here
        $data["title"] = "Flagbook - Admin";

        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );
        return $response;
    }
}
