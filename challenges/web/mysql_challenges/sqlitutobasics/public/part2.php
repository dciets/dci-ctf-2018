<?php
require_once __DIR__.'/utils.php';

if ($_SESSION["level"] < 2) {
    header('Location: part1.php');
    exit(1);
}

ob_start();
include 'api/part2.php';
$response = json_decode(ob_get_contents(), true);
ob_end_clean();

$username = $response["username"];
$password = $response["password"];
$mysql_output = $response["sqlout"];
$php_output = $response["phpout"];
$php_code = get_sources("api/part2.php", 10, 31);

$level3_status = $_SESSION["level"] >= 3 ? "" : "disabled";
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/highlight/default.css">
    <link rel="stylesheet" href="assets/css/highlight/vs2015.css">
    <link rel="stylesheet" href="assets/css/styles.css">

    <title>SQLi Basics 2</title>
</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Basics 1 </a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="/part2.php">Basics 2 <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $level3_status; ?>" id="next_chal" href="/part3.php">Basics 3</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="main-container">
        <div class="row">
            <div class="starter-template">
                <h1>Basics 2</h1>
                <p class="lead">Order of operations</p>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-6">
                <h5>PHP code <a class="btn btn-primary" data-toggle="collapse" href="#phpcode" role="button" aria-expanded="true" aria-controls="phpcode">Toggle</a></h5>
                <pre><code id="phpcode" class="collapse show mycode"><?php echo $php_code; ?></code></pre>

                <h5>MySQL command <a class="btn btn-primary" data-toggle="collapse" href="#sqlcode" role="button" aria-expanded="true" aria-controls="sqlcode">Toggle</a></h5>
                <pre><code id="sqlcode" class="collapse show mycode"></code></pre>
                <h5>PHP output <a class="btn btn-primary" data-toggle="collapse" href="#phpout" role="button" aria-expanded="true" aria-controls="phpout">Toggle</a></h5>
                <pre><code id="phpout" class="collapse show mycode json"><?php echo $php_output; ?></code></pre>
                <h5>MySQL output <a class="btn btn-primary" data-toggle="collapse" href="#sqlout" role="button" aria-expanded="true" aria-controls="sqlout">Toggle</a></h5>
                <pre><code id="sqlout" class="collapse show mycode json"><?php echo $mysql_output; ?></code></pre>
            </div>
            <div class="col-lg-6 login-form">
                <div class="panel row" style="text-align: center">
                    <div class="col">
                        <h5>Login</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <form method="POST" action="">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="username" name="username" id="name_input" onkeydown="input_changed()" value="<?php echo $username; ?>" />
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="password" name="password" id="pwd_input" onkeydown="input_changed()" value="<?php echo $password; ?>" />
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;" name="login">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- /.container -->

    <script src="assets/js/jquery-3.3.1.slim.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <script>
        var interval;
        var phpout     = document.getElementById('phpout');
        var sqlout     = document.getElementById('sqlout');
        var name_input = document.getElementById("name_input");
        var pwd_input  = document.getElementById("pwd_input");
        var sqlcode    = document.getElementById("sqlcode");
        var loading_img = '<img src="assets/img/loading.gif" height=25 width=25 />'
        update_sql();

        function update_sql() {
            sql  = "SELECT * FROM users2 WHERE username != 'admin' AND (username = '";
            sql += name_input.value;
            sql += "' AND password = '";
            sql += pwd_input.value;
            sql += "')";

            sqlcode.textContent = sql;
            hljs.initHighlighting.called = false;
            hljs.initHighlighting();
        }

        function input_changed() {
            setTimeout(() => {
                clearTimeout(interval);
                update_sql();
                phpout.innerHTML = loading_img;
                sqlout.innerHTML = loading_img;

                interval = setTimeout(function() {
                    var http = new XMLHttpRequest();
                    var url = 'api/part2.php';
                    params = 'username=' + name_input.value;
                    params += "&password=" + pwd_input.value;
                    params += "&login=1";

                    http.open('POST', url, true);

                    //Send the proper header information along with the request
                    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    http.onreadystatechange = function() {
                        if (http.readyState == 4 && http.status == 200) {
                            var response = JSON.parse(this.responseText);
                            phpout.innerHTML = response.phpout;
                            sqlout.innerHTML = response.sqlout;

                            if (response.success) {
                                document.getElementById("next_chal").classList.remove('disabled');
                            }
                            hljs.initHighlighting.called = false;
                            hljs.initHighlighting();
                        }
                    }
                    http.send(params);
                }, 1000);
            }, 50);
        }
    </script>
</body>
</html>