<?php

class FileView extends View
{
    public function show($templates) {
        $file = $_GET["url"];
        $banned = array("Router", "Page", "Application", "controllers", "views", "templates", "models", "flag");
        foreach($banned as $ban) {
            if (strrpos($file, $ban) !== false) {
                echo "The path to this file contains a banned word: '". $ban . "', and cannot be displayed.</br>";
                echo "Other banned words are: <pre>";
                print_r($banned);
                echo "</pre>";
                exit(1);
            }
        }

        $type = $_GET["type"];
        if ($type = "css") {
            $contentType = "text/css";
        } elseif ($type == "js") {
            $contentType = "text/javascript";
        } else if ($type="image") {
            $contentType = "image/png";
        }


        $fp = fopen($file, 'rb');
        header("Content-type: ". $contentType);
        fpassthru($fp);
    }
}

