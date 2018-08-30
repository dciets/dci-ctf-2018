<?php
require_once __DIR__.'/utils.php';

if ($_SESSION["level"] < 3) {
    header('Location: part2.php');
    exit(1);
}

ob_start();
include 'api/part3.php';
$response = json_decode(ob_get_contents(), true);
ob_end_clean();

$id = $response["id"];
$mysql_output = $response["sqlout"];
$php_output = $response["phpout"];
$php_code = get_sources("api/part3.php", 10, 28);
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

    <title>SQLi Filters 3</title>
</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Filters 1 </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/part2.php">Filters 2</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/part3.php">Filters 3 <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="main-container">
        <div class="row">
            <div class="starter-template">
                <h1>Filters 3</h1>
                <p class="lead">Flag is hidden somewhere... <a href="https://dev.mysql.com/doc/refman/8.0/en/information-schema.html">INFORMATION_SCHEMA</a> might be useful.</p>
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
            <div class="col-lg-6 search-form">
                <div class="panel row" style="text-align: center">
                    <div class="col">
                        <h5>User search</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <form method="POST" action="">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="User id" name="id" id="id_input" onkeydown="input_changed()" value="<?php echo $id; ?>" />
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;" name="login">Search</button>
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

        function filter_js(str) {
            str = str.replace(/\s+/g, '');
            str = str.replace(/['"#\-\*/=<>&|]/g, '');
            return str;
        }

        function update_sql() {
            sql  = "SELECT * FROM users6 WHERE id = ";
            sql += filter_js(id_input.value);
            sql += " LIMIT 1";

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
                    var url = 'api/part3.php';
                    params = 'id=' + id_input.value;
                    params += "&search=1";

                    http.open('POST', url, true);

                    //Send the proper header information along with the request
                    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    http.onreadystatechange = function() {
                        if (http.readyState == 4 && http.status == 200) {
                            var response = JSON.parse(this.responseText);
                            phpout.innerHTML = response.phpout;
                            sqlout.innerHTML = response.sqlout;
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
