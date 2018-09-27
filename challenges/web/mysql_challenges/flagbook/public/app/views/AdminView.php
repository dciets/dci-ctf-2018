<?php

class AdminView extends View
{
    public function show($templates) {
        $templates = array("main" => "admin.php", "content" => "base.php");
        parent::show($templates);
    }
}
