<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.6.8-fix/jquery.nicescroll.min.js"></script>
<?php if (count($this->data["data"]["friends"]) === 0): ?>
  <div class="col-md-12">
    <!-- edit profile -->
    <div class="panel panel-default">
        <div class="panel-body">
          <p>A chat client is useless without friends...</p>
        </div>
    </div>
  </div>
<?php else: ?>
  <div class="content container-fluid bootstrap snippets">
        <div class="row row-broken">
          <div class="col-sm-3 col-xs-12">
            <div class="col-inside-lg decor-default chat" style="overflow: hidden; outline: none;" tabindex="5000">
              <div class="chat-users">
                  <h6>Online</h6>
                  <?php foreach($this->data["data"]["friends"] as $friend): ?>
                    <div class="user">
                        <div class="avatar">
                          <img src="/api/image?url=uploads/avatar.png" alt="avatar">
                          <div class="status online"></div>
                        </div>
                        <a class="name" href="/messenger?s=<?= $_SESSION["user_id"]; ?>&r=<?= $friend["id"]; ?>"><?= e($friend["username"]); ?></a>
                        <div class="mood"><?= $friend["status"]; ?></div>
                    </div>
                  <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="col-sm-9 col-xs-12 chat" style="overflow: hidden; outline: none;" tabindex="5001">
            <div class="col-inside-lg decor-default">
              <div class="chat-body">
                <h6>Mini Chat</h6>
                <?php foreach ($this->data["data"]["messages"] as $message): ?>
                  <?php if ($message["sender_id"] == $_GET["s"]): ?>
                    <div class="answer right">
                  <?php else: ?>
                    <div class="answer left">
                  <?php endif; ?>
                      <div class="avatar">
                        <img src="/api/image?url=uploads/avatar.png" alt="User name">
                        <div class="status online"></div>
                      </div>
                      <div class="name"><?= e($message["username"]) ?></div>
                      <div class="text">
                        <?= e($message["content"]); ?>
                      </div>
                      <div style="margin-bottom:15px"></div>
                    </div>
                <?php endforeach; ?>
                <div class="answer-add">
                  <form method="POST" action="">
                    <div class="input-group">
                        <input class="" style="line-height: 30px;" type="text" name="content" placeholder="Write something...">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit" name="submit_message">Post</button>
                        </span>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div style="margin-bottom: 20px"></div>
  <script>
  $(function(){
      $(".chat").niceScroll();
  }) 
  </script>
<?php endif; ?>