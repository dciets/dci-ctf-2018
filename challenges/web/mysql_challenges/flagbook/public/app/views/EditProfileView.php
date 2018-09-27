<?php

class EditProfileView extends View
{
    public function show($templates) {
        $templates = array("main" => "editprofile.php", "content" => "base.php");
        parent::show($templates);
    }
}

