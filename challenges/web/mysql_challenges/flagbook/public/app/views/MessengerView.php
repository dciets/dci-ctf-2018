<?php

class MessengerView extends View
{
    public function show($templates) {
        $templates = array("main" => "messenger.php", "content" => "base.php");
        parent::show($templates);
    }
}

