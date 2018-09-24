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

$wall = array();
try {
    $sql = "SELECT * FROM wall_of_shame";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $wall = $stmt->fetchAll();
} catch (Exception $e) {
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

    <title>Wall of Shame</title>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Flag</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/wallofshame.php">Wall of Shame <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="main-container">
        <div class="row">
            <?php foreach($wall as $card): ?>
                <div class="card shamed">
                    <img class="card-img-top" src="<?php echo $card['image_path']; ?>" alt="Card image cap">
                    <div class="card-body">
                        <p class="card-text"><?php echo $card['comment']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>


    <script src="assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
