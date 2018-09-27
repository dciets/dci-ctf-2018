<?php if (isset($_SESSION["user_id"])): ?>
    <div class="row">
        <div class="col-md-3">
            <!-- profile brief -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4><?= e($_SESSION["username"]); ?></h4>
                    <p><?= e($_SESSION["status"]); ?></p>
                </div>
            </div>
            <!-- ./profile brief -->

            <!-- friend requests -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>friend requests</h4>
                    <div id="requests-div">
                        <ul>
                        <?php foreach ($this->data["data"]["requests"] as $friend): ?>
                            <li>
                                <a href="/profile?id=<?= $friend["id"]; ?>"><?= e($friend["username"]); ?></a> 
                                <a class="text-success ajax-link" href="/api/friends?action=accept&friend_id=<?= $friend["id"]; ?>"> [accept]</a>
                                <a class="text-danger ajax-link" href="/api/friends?action=decline&friend_id=<?= $friend["id"]; ?>"> [decline]</a>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ./friend requests -->
        </div>

        <div class="col-md-6">
            <!-- post form -->
            <form method="POST" action="" enctype="multipart/form-data" id="post_form">
                <div class="input-group">
                    <span class="input-group-btn">
                        <span class="btn btn-success" id="file_button">+</span>
                    </span>
                    <input class="form-control" type="text" name="content" placeholder="Make a post...">
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit" id="submit_post" name="submit_post">Post</button>
                    </span>
                </div>
                <input type="file" id="post_file" accept="image/*" name="post_file" style="display: none;" />
            </form>
            <div class="card" style="background-color: black;">
                <img class="card-img-top center-block" id="file_preview" style="max-width: 100%; max-height: 300px;" src="" />
            </div>
            <hr>
            <!-- ./post form -->

            <!-- feed -->
            <div>
            <?php foreach($this->data["data"]["posts"] as $post): ?>
                <!-- post -->
                <div class="panel panel-default" style="border: 0px;">
                    <?php if ($post["content"] !== ""): ?>
                    <div class="panel-body">
                        <p><?= e($post["content"]); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($post["image_path"] !== ""): ?>
                    <div class="card" style="background-color: black">
                        <img class="card-img-top center-block" style="max-width: 100%; max-height: 300px;" src="/api/image?url=<?= urlencode($post["image_path"]); ?>" alt="Card image cap" />
                    </div>
                    <?php endif; ?>
                    <div class="panel-footer">
                        <span>posted <?= $post["post_time"]; ?> by <?= e($post["username"]); ?></span> 
                    </div>
                </div>
                <!-- ./post -->
            <?php endforeach; ?>
            </div>
            <!-- ./feed -->
        </div>

        <div class="col-md-3">
            <!-- add friend -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>People at your location</h4>
                    <div id="suggestions-div">
                        <ul>
                        <?php foreach ($this->data["data"]["suggestions"] as $friend): ?>
                            <li>
                                <a href="/profile?id=<?= $friend["id"]; ?>"><?= e($friend["username"]); ?></a> 
                                <a class="ajax-link" href="/api/friends?action=accept&friend_id=<?= $friend["id"]; ?>""> [add]</a>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ./add friend -->

            <!-- friends -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <h4>Friends</h4>
                    <div id="friends-div">
                        <?php if (count($this->data["data"]["friends"]) === 0): ?>
                            <p>You have absolutely no friends.</p>
                        <?php else: ?>
                            <ul>
                            <?php foreach ($this->data["data"]["friends"] as $friend): ?>
                                <li>
                                    <a href="/profile?id=<?= $friend["id"]; ?>"><?= e($friend["username"]); ?></a> 
                                    <a class="text-danger ajax-link" href="/api/friends?action=decline&friend_id=<?= $friend["id"]; ?>"> [unfriend]</a>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- ./friends -->
        </div>
    </div>
<?php else: ?>
    <h1 class="text-center">Welcome to Flagbook! <br><small>The best social networking website for people who love collecting flags.</small></h1>

    <div class="row">
        <div class="col-md-6">
            <h4>Login to start enjoying unlimited fun!</h4>

            <!-- login form -->
            <form method="POST" action="">
                <div class="form-group">
                    <input class="form-control" type="text" name="username" placeholder="Username">
                </div>

                <div class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </div>

                <div class="form-group">
                    <input class="btn btn-primary" type="submit" name="login" value="Login">
                </div>
            </form>
            <!-- ./login form -->
            </br>
        </div>

        <div class="col-md-6">
            <h4>Don't have an account yet? Register!</h4>

            <!-- register form -->
            <form method="POST" action="">
                <div class="form-group">
                    <input class="form-control" type="text" name="username" placeholder="Username">
                </div>

                <div class="form-group">
                    <input class="form-control" type="password" name="password" placeholder="Password">
                </div>

                <div class="form-group">
                    <input class="form-control" type="password" name="repeat" placeholder="Repeat password">
                </div>

                <div class="form-group">
                    <input class="btn btn-success" type="submit" name="submit_signup" value="Register">
                </div>
            </form>
            <!-- ./register form -->
        </div>
    </div>
<?php endif; ?>
<script>
$('#file_button').click(function() {
    $('#post_file').click();
});

$("#post_file").change(function() {
    extras = {path: "uploads/tmp/"}
    submit_form("/api/file-upload", "post_form", extras, function(resp) {
        $('#file_preview').attr('src', "/api/image?url="+resp.data.path);
    });
});

function ajax_link_handler(event) {
    event.preventDefault(); 
    $.ajax({
        url: $(this).attr('href'),
        dataType: "json",
        success: function(response) {
            update_friends(response.data);
            update_suggestions(response.data);
            update_requests(response.data);
        }
    });
    return false;
}

$('#friends-div').on('click', '.ajax-link', ajax_link_handler);
$('#suggestions-div').on('click', '.ajax-link', ajax_link_handler);
$('#requests-div').on('click', '.ajax-link', ajax_link_handler);

function update_suggestions(data) {
    if (data.suggestions.length > 0) {
        text = "<ul>"
        data.suggestions.forEach(function(friend) {
            text += "<li><a href='/profile?id=" + friend.id + "'>" + e(friend.username) + "</a>";
            text += "<a class='ajax-link' href='/api/friends?action=accept&friend_id=" + friend.id + "'> [add]</a></li>";
        });
        text += "</ul>"
        $('#suggestions-div').html(text)
    } else {
        $('#suggestions-div').html("")
    }
}

function update_friends(data) {
    if (data.friends.length === 0) {
        $('#friends-div').html("<p>You have absolutely no friend.</p>")
    } else {
        text = "<ul>"
        data.friends.forEach(function(friend) {
            text += "<li><a href='/profile?id=" + friend.id + "'>" + e(friend.username) + "</a>";
            text += "<a class='text-danger ajax-link' href='/api/friends?action=decline&friend_id=" + friend.id + "'> [unfriend]</a></li>";
        });
        text += "</ul>"
        $('#friends-div').html(text)
    }
}

function update_requests(data) {
    if (data.requests.length > 0) {
        text = "<ul>"
        data.requests.forEach(function(friend) {
            text += "<li><a href='/profile?id=" + friend.id + "'>" + e(friend.username) + "</a>";
            text += "<a class='text-success ajax-link' href='/api/friends?action=accept&friend_id=" + friend.id + "'> [accept]</a>";
            text += "<a class='text-danger ajax-link' href='/api/friends?action=decline&friend_id=" + friend.id + "'> [decline]</a></li>";
        });
        text += "</ul>"
        $('#requests-div').html(text)
    } else {
        $('#requests-div').html("")
    }
}

</script>