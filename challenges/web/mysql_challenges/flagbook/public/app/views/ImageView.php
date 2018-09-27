<?php

class ImageView extends View
{
    public function show($templates) {
        $image = $_GET["url"];
        $banned = array("Router", "Page", "Application", "controllers", "views", "templates", "models", "flag");
        foreach($banned as $ban) {
            if (strrpos($image, $ban) !== false) {
                echo "The path to this image contains a banned word: '". $ban . "', and cannot be displayed.</br>";
                echo "Other banned words are: <pre>";
                print_r($banned);
                echo "</pre>";
                exit(1);
            }
        }


        $fp = fopen($image, 'rb');
        header("Content-type: image/png;");
        fpassthru($fp);
    }
}
