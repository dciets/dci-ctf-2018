<?php

class ProfileView extends View
{
    public function show($templates) {
        $templates = array("main" => "profile.php", "content" => "base.php");
        parent::show($templates);
    }
}

