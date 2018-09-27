<?php

class HomeController extends Controller
{
    public function processRequest() {
        $data = parent::processRequest();

        // process request here
        $data["title"] = "Flagbook - Home";
        $data = $this->handleLogin($data);
        $this->init_controllers("");
        $data = $this->handleSignup($data);
        $data = $this->handleNewPost($data);

        
        $response = array(
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $data
        );


        if (isset($_SESSION["user_id"])) {
            $posts = APIController::get_visible_posts($_SESSION["user_id"]);
            $friends = APIController::get_friends_data($_SESSION["user_id"]);
            $response = $this->merge_response($response, $posts);
            $response = $this->merge_response($response, $friends);
        }
        return $response;
    }

    public function handleLogin($data) {
        if (isset($_POST["login"])) {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $username = isset($_POST["username"]) ? $_POST["username"] : "";
            $password = isset($_POST["password"]) ? $_POST["password"] : "";
            $password = md5($password);

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->bindParam(1, $username, PDO::PARAM_STR);
            $stmt->bindParam(2, $password, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) === 0) {
                $this->status = 0;
                $this->messages[] = "Invalid username/password.";
                return $data; 
            }

            $_SESSION["user_id"] = $result[0]["id"];
            $_SESSION["username"] = $result[0]["username"];
            $_SESSION["status"] = $result[0]["status"];
            $_SESSION["location"] = $result[0]["location"];
            $_SESSION["permission"] = $result[0]["role"];
        }
        return $data;
    }

    public function handleSignup($data) {
        if (isset($_POST["submit_signup"])) {
            $db = db_connect(MYSQL_DEFAULT_USERNAME, MYSQL_DEFAULT_PASSWORD);
            $username = isset($_POST["username"]) ? $_POST["username"] : "";
            $password = isset($_POST["password"]) ? $_POST["password"] : "";
            $repeat = isset($_POST["repeat"]) ? $_POST["repeat"] : "";
            $messages = array();

            if (strlen($username) === 0 || strlen($username) > 16) {
                $this->messages[] = "Username cannot be empty or longer than 16 characters.";
            } else {
                $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bindParam(1, $username, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() !== 0) {
                    $this->messages[] = "Username is already taken.";                
                }
            }

            if (strlen($password) === 0) {
                $this->messages[] = "Password cannot be empty.";
            }

            if ($password !== $repeat) {
                $this->messages[] = "Password and password repeat mismatch.";
            }

            if (count($this->messages) !== 0) {
                $this->status = 0;
                return $data;
            }

            $password = md5($password);
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES(?, ?)");
            $stmt->bindParam(1, $username, PDO::PARAM_STR);
            $stmt->bindParam(2, $password, PDO::PARAM_STR);
            $stmt->execute();

            $_SESSION["user_id"] = $db->lastInsertId();
            $_SESSION["username"] = $username;
            $_SESSION["status"] = "";
            $_SESSION["location"] = "Canada"; 
            $_SESSION["permission"] = User::USER;
            $this->status = 1;
            $this->messages[] = "Your account was successfully created.";
        }
        return $data;
    }

    function handleNewPost($data) {
        if (isset($_POST["submit_post"])) {
            $data["user"]->check_permission(User::CONNECTED);
            $response = APIController::submit_post();
            $data = array_merge($data, $response["data"]);
            if (!$response["status"]) {
                $this->status = 0;
                $this->messages = array_merge($this->messages, $response["messages"]);
            }
        }
        return $data;
    }
}