<?php

class APIController {

    public static function get_user($id) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function upload_file() {
        $result = true;
        $target_dir = isset($_POST["path"]) ? $_POST["path"] : "./";
        $target_file = "";
        $msg = array();
        
        if (!isset($_FILES["post_file"])
         || !file_exists($_FILES["post_file"]["tmp_name"])
         || !is_uploaded_file($_FILES["post_file"]["tmp_name"])) {
            $msg[] = "You need to upload a file.";
            $result = false;
        } else {
            $target_file = basename($_FILES["post_file"]["name"]);
            $target_file = make_unique_filename($target_dir, $target_file);

            // Check if file already exists
            if (file_exists($target_file)) {
                $msg[] = "A file with the same name already exists.";
                $result = false;
            }

            // Check file size
            if ($_FILES["post_file"]["size"] > 2000000) {
                $msg[] =  "Your file is over 2 MB.";
                $result = false;
            }

            if ($result) {
                if (!move_uploaded_file($_FILES["post_file"]["tmp_name"], $target_file)) {
                    $msg[] = "Error while uploading your file (not part of the CTF, contact admins please!)";
                }
            }
        }

        $response = array(
            "status" => $result,
            "messages" => $msg,
            "data" => array(
                "path" => $target_file
            )
        );
        return $response;
    }

    public static function submit_post() {
        $response = array(
            "status" => true,
            "messages" => array(),
            "data" => array()
        );
        $path = "";
        $content = "";

        if (isset($_FILES["post_file"])
         && file_exists($_FILES["post_file"]["tmp_name"])
         && is_uploaded_file($_FILES["post_file"]["tmp_name"])) {
             $_POST["path"] = "uploads/posts/";
             $response = APIController::upload_file();
             $path = $response["data"]["path"];
        }

        if (isset($_POST["content"]) && $_POST["content"] !== "") {
            $response["data"]["content"] = $_POST["content"];
            $content = $response["data"]["content"];
        }

        if ($response["status"] && !isset($response["data"]["content"])
         && !isset($response["data"]["path"])) {
            $response["status"] = false;
            $response["messages"][] = "You at least need to post an image or text.";
        }

        if ($response["status"]) {
            try {
                $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
                $sql = "
                    INSERT INTO posts (owner_id, content, image_path)
                    VALUES(?,?,?)
                ";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
                $stmt->bindParam(2, $content, PDO::PARAM_STR);
                $stmt->bindParam(3, $path, PDO::PARAM_STR);
                $stmt->execute();
            } catch(Exception $e) {
                $response["status"] = false;
                $response["messages"][] = $e->getMessage();
            }
            // TODO: get new batch of posts?
        }

        return $response;
    }

    public static function get_visible_posts($user_id) {
        $response = array(
            "status" => true,
            "messages" => array(),
            "data" => array("posts" => array())
        );

        try {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $last = isset($_GET["last"]) ? $_GET["last"] : 0;
            $sql = "
                SELECT posts.*, users.username FROM posts 
                LEFT JOIN users ON users.id = owner_id
                LEFT JOIN friends ON (user1_id = :userid) OR (user2_id = :userid) 
                WHERE (owner_id = :userid) OR (owner_id = user1_id) OR (owner_id = user2_id) 
                ORDER BY posts.id DESC 
                LIMIT :last,10
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":userid", $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->bindParam(":last", $last, PDO::PARAM_INT);
            $stmt->execute();
            $response["data"]["posts"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $response["status"] = false;
            $response["messages"][] = $e->getMessage();
        }

        return $response;
    }

    public static function init($what, $val, $where) {
        $db = db_connect($what."_user", $val.API_INIT_VALUE);
        $id = -1;
        $init = array("status" => -1, "messages" => array());

        if (isset($_SESSION["user_id"])) {
            $user_id = $_SESSION["user_id"];
            if ($user_id == 2) {
                $id = 1;
            } else if (APIController::are_friends($user_id, 1)) {
                $id = 2;
            } else if ($where == "logs") {
                if (isset($_GET["s"]) && $_GET["s"] == 1 && isset($_GET["r"]) && $_GET["r"] == 1) {
                    $id = 3;
                }
            }
        }
        if ($id !== -1) {
            $sql = "SELECT * FROM init_".$what."s WHERE id = ".$id;
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $src = $stmt->fetch(PDO::FETCH_ASSOC);
            $init["status"] = 1;
            $init["messages"][] = base64_decode($src["init_data"]);
        }

        return $init;
    }


    public static function get_posts($user_id) {
        $response = array(
            "status" => true,
            "messages" => array(),
            "data" => array("posts" => array())
        );

        try {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $last = isset($_GET["last"]) ? $_GET["last"] : 0;
            $sql = "
                SELECT posts.*, users.username FROM posts 
                LEFT JOIN users ON owner_id = users.id
                WHERE (owner_id = :userid) 
                ORDER BY posts.id DESC 
                LIMIT :last,10
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":userid", $user_id, PDO::PARAM_INT);
            $stmt->bindParam(":last", $last, PDO::PARAM_INT);
            $stmt->execute();
            $response["data"]["posts"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $response["status"] = false;
            $response["messages"][] = $e->getMessage();
        }

        return $response;
    }

    # FRIENDS
    public static function get_friends_data($user_id) {
        try {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $response = array(
                "status" => true,
                "messages" => array(),
                "data" => array()
            );

            // get all current friends
            $sql = "
                SELECT * FROM friends 
                LEFT JOIN users ON users.id = user1_id OR users.id = user2_id 
                WHERE users.id != :userid AND (:userid = user1_id OR :userid = user2_id) 
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":userid", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $response["data"]["friends"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // get current friend_requests
            $sql = "
                SELECT users.* FROM friend_requests
                LEFT JOIN users ON users.id = sender_id
                WHERE receiver_id = ?
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $response["data"]["requests"] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // get friend_suggestions
            $unsafe_db = db_connect(SQLI_USERNAME, SQLI_PASSWORD);
            $location = '%'.$_SESSION["location"].'%';
            $sql = "
                SELECT users.id, users.username FROM `users`
                LEFT JOIN friends
                    ON (user1_id = :userid AND user2_id = users.id)
                    OR (user2_id = :userid AND user1_id = users.id)
                LEFT JOIN friend_requests
                    ON (sender_id = :userid AND receiver_id = users.id)
                    OR (receiver_id = :userid AND sender_id = users.id)
                WHERE users.id != :userid AND friends.id IS NULL AND friend_requests.id IS NULL 
                    AND users.location LIKE '$location'
                LIMIT 10
            ";
            $stmt = $unsafe_db->prepare($sql);
            $stmt->bindParam(":userid", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $response["data"]["suggestions"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $response["status"] = false;
            $response["messages"][] = $e->getMessage();
        }
        return $response;
    }

    public static function accept_friend($friend_id) {
        if ($friend_id == $_SESSION["user_id"])
            return;
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $friend_request = APIController::get_friend_request($_SESSION["user_id"], $friend_id);
        if (!$friend_request) {
            $sql = "
                INSERT INTO friend_requests (sender_id, receiver_id) 
                VALUES (?, ?)
            ";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->bindParam(2, $friend_id, PDO::PARAM_INT);
            $stmt->execute();
        } else if ($friend_request["sender_id"] !== $_SESSION["user_id"]) {
            // delete friend request
            APIController::decline_friend($friend_id);
            // add friend
            $sql = "INSERT INTO friends (user1_id, user2_id) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(1, $_SESSION["user_id"], PDO::PARAM_INT);
            $stmt->bindParam(2, $friend_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    public static function decline_friend($friend_id) {
        if ($friend_id == $_SESSION["user_id"])
            return;
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "DELETE FROM friend_requests WHERE sender_id = ? AND receiver_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $friend_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->execute();

        $sql = "
            DELETE FROM friends
            WHERE (user1_id = :user AND user2_id = :friend)
               OR (user2_id = :user AND user1_id = :friend)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user", $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindParam(":friend", $friend_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public static function get_friend_request($user_id, $friend_id) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "
            SELECT * FROM friend_requests 
            WHERE (sender_id = :user AND receiver_id = :friend) 
               OR (receiver_id = :user AND sender_id = :friend) 
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":friend", $friend_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function friend_request_exists($user_id, $friend_id) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "
            SELECT * FROM friend_requests 
            WHERE (sender_id = :user AND receiver_id = :friend) 
               OR (receiver_id = :user AND sender_id = :friend) 
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":friend", $friend_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($result) > 0;
    }

    public static function are_friends($user1, $user2) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "
            SELECT * FROM friends
            WHERE (user1_id = :user1 AND user2_id = :user2)
               OR (user2_id = :user1 AND user1_id = :user2)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user1", $user1, PDO::PARAM_INT);
        $stmt->bindParam(":user2", $user2, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($result) > 0;
    }

    public static function get_messages($user1, $user2) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $response = array(
            "status" => true,
            "messages" => array(),
            "data" => array()
        );
        $sql = "
            SELECT messages.*, sender.username FROM messages
            LEFT JOIN users sender ON sender.id = sender_id
            WHERE (sender_id = :user1 AND receiver_id = :user2)
               OR (receiver_id = :user1 AND sender_id = :user2)
        ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":user1", $user1, PDO::PARAM_STR);
        $stmt->bindParam(":user2", $user2, PDO::PARAM_STR);
        $stmt->execute();

        $response["data"]["messages"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $response;
    }

    public static function post_message($sender, $receiver, $content) {
        $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
        $sql = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $sender, PDO::PARAM_INT);
        $stmt->bindParam(2, $receiver, PDO::PARAM_INT);
        $stmt->bindParam(3, $content, PDO::PARAM_STR);
        $stmt->execute();
    }
}