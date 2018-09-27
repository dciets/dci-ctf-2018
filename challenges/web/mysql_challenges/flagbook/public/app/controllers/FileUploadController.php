<?php

class FileUploadController extends Controller
{
    public function __construct() {
        $data = parent::processRequest();
        $data["user"]->check_permission(User::CONNECTED);

        // process request here
        $data = $this->handleFileUpload($data);

        return $data;
    }

    public function handleFileUpload($data) {
        $response = APIController::upload_file();
        echo json_encode($response);
        exit(1);
    }
}


