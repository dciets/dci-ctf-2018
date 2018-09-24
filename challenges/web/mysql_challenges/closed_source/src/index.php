<?php
$user = "wall";
$pwd  = "Zhu58JV5rJkpSZGa";
$servername = getenv("MYSQL_HOST");
$dbname = "closed_source";

$db = new PDO("mysql:host=$servername;dbname=$dbname", $user, $pwd, 
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SESSION sql_mode=""'));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (!$db) {
    die("Connection failed: " . $db->connect_error);
}


if (isset($_POST["submit-shame"])) {
    $result = true;
    $target_dir = "uploads/";
    $msg = array();
    
    if (!isset($_FILES["picture"])
     || !file_exists($_FILES["picture"]["tmp_name"])
     || !is_uploaded_file($_FILES["picture"]["tmp_name"])) {
        $msg[] = "You need to upload a picture.";
        $result = false;
    } else {
        $target_file  = $target_dir;
        $target_file .= basename($_FILES["picture"]["tmp_name"]);
        $target_file .= basename($_FILES["picture"]["name"]);
        $comment = isset($_POST["comment"]) ? $_POST["comment"] : "";

        // Check if file already exists
        if (file_exists($target_file)) {
            $msg[] = "A file with the same name already exists.";
            $result = false;
        }

        // Check file size
        if ($_FILES["picture"]["size"] > 2000000) {
            $msg[] =  "Your file is over 2 MB.";
            $result = false;
        }

        if ($result) {
            if (move_uploaded_file($_FILES["picture"]["tmp_name"], $target_file)) {
                try {
                    $sql = "INSERT INTO wall_of_shame (image_path, comment) VALUES(?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute(array($target_file, $comment));
                } catch (Exception $e) {
                    $msg[] = "Error inserting into database.";
                    $result = false;
                }
            } else {
                $msg[] = "Error while uploading your file (not part of the CTF, contact admins please!)";
            }
        }
    }
}

$target = array(
    "50c9e8d5fc98727b4bbc93cf5d64a68db647f04f",
    "32096c2e0eff33d844ee6d675407ace18289357d",
    "ca73ab65568cd125c2d27a22bbd9e863c10b675d",
    "60ba4b2daa4ed4d070fec06687e249e0e6f9ee45",
    "4a0a19218e082a343a1b17e5333409af9d98f0f5",
    "1b6453892473a467d07372d45eb05abc2031647a",
    "902ba3cda1883801594b6e1b452790cc53948fda",
    "fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f",
    "4a0a19218e082a343a1b17e5333409af9d98f0f5",
    "b6589fc6ab0dc82cf12099d1c2d40ab994e8410c",
    "e9d71f5ee7c92d6dc9e92ffdad17b8bd49418f98",
    "4a0a19218e082a343a1b17e5333409af9d98f0f5",
    "77de68daecd823babbb58edb1c8e14d7106e83bb",
    "77de68daecd823babbb58edb1c8e14d7106e83bb",
    "0ade7c2cf97f75d009975f4d720d1fa6c19f4897",
    "3c363836cf4e16666669a25da280a1865c2d2874",
    "0ade7c2cf97f75d009975f4d720d1fa6c19f4897",
    "fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f",
    "3c363836cf4e16666669a25da280a1865c2d2874",
    "da4b9237bacccdf19c0760cab7aec4a8359010b0",
    "86f7e437faa5a7fce15d1ddcb9eaeaea377667b8",
    "77de68daecd823babbb58edb1c8e14d7106e83bb",
    "86f7e437faa5a7fce15d1ddcb9eaeaea377667b8",
    "356a192b7913b04c54574d18c28d46e6395428ab",
    "ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4",
    "3c363836cf4e16666669a25da280a1865c2d2874",
    "1b6453892473a467d07372d45eb05abc2031647a",
    "86f7e437faa5a7fce15d1ddcb9eaeaea377667b8",
    "fe5dbbcea5ce7e2988b8c69bcfdfde8904aabc1f",
    "ac3478d69a3c81fa62e60f5c3696165a4e5e6ac4",
    "c1dfd96eea8cc2b62785275bca38ac261256e278",
    "c2b7df6201fdd3362399091f0a29550df3505b6a",
);

if (isset($_POST["flag"])) {
    $result = false;
    $flag = $_POST["flag"];
    if (strlen($flag) == count($target)) {
        $result = true;
        foreach (str_split($flag) as $key => $c) {
            if (sha1($c) != $target[$key]) {
                $result = false;
                break;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">

    <title>Index</title>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Flag <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/wallofshame.php">Wall of Shame</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="main-container">
        <div class="row">
            <h5>Prove you are a good hacker by finding the flag:</h5>
            <form style="margin-left: 20px;" method="POST" action="">
                <div class="form-group" style="position: relative; bottom: 5px;">
                    <input type="text" name="flag" style="width: 300px;" placeholder="DCI{........................................}"/>
                    <input type="submit" name="submit-flag" value="Submit"/>
                </div>
                <div>
                </div>
            </form>
        </div>
        <hr style="background-color: #4d544d; margin-bottom: 50px;">
        <div>
            <?php if (isset($_POST["flag"])): ?>
                <?php if (!$result): ?>
                    <h5>Wrong! You should add your picture to the <a href="/wallofshame.php">Wall of Shame</a> so that everyone may know of your failure.</h5>
                    <div class="row" style="margin-top: 30px">
                        <form style="margin-left: 20px;" method="POST" action="" enctype='multipart/form-data'>
                            <div class="form-group">
                                <input type="file" class="form-control-file" style="margin-bottom:10px;" name="picture"/>
                                <textarea class="form-control" rows=3 cols=50 style="margin-bottom:10px;" placeholder="Comments?" name="comment"></textarea>
                                <input type="submit" name="submit-shame" value="Submit"/>
                            </div>
                            <div>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <h5 style="color: green;">Good job, this is the correct flag!</h5>
                <?php endif; ?>
            <?php elseif (isset($_POST["submit-shame"])): ?>
                <?php if ($result): ?>
                    <h5 style="color: green;">Your picture was correctly added to the <a href="/wallofshame.php">Wall of Shame</a>.</h5>
                <?php else: ?>
                    <h5 style="color: red;">Error(s):</h5>
                    <div class="row" style="margin-top: 30px;">
                        <ul>
                            <?php foreach($msg as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        <ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </main>


    <script src="assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>