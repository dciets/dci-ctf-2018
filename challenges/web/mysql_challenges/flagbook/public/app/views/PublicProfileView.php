<?php

class PublicProfileView extends View
{
    public function show($templates) {
        $templates = array("main" => "publicprofile.php", "content" => "base.php");
        parent::show($templates);
    }
}


