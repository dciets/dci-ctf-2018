<?php

class Error404Controller extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();

        // process request here
        $data["title"] = "Flagbook - 404";

        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );
        return $response;
    }
}