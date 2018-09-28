<div class="row">
      <div class="col-md-6">
        <!-- user profile -->
        <div class="media">
          <div class="media-left">
            <img src="/api/file?url=uploads/avatar.png&type=image" class="media-object" style="width: 128px; height: 128px;">
          </div>
          <div class="media-body">
            <h2 class="media-heading">Name: <?= e($this->data["data"]["username"]); ?></h2>
            <p>Status: <?= e($this->data["data"]["status"]); ?></p>
          </div>
        </div>
        <!-- user profile -->

        <hr>

        <!-- feed -->
        <div>
        <?php if (!$this->data["data"]["is_friend"]): ?>
            <p><?= e($this->data["data"]["username"]); ?> is not your friend, you can't see their posts.</p>
        <?php elseif (count($this->data["data"]["posts"]) === 0): ?>
            <p><?= e($this->data["data"]["username"]); ?> has not posted anything yet.</p>
        <?php else: ?>
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
                        <img class="card-img-top center-block" style="max-width: 100%; max-height: 300px;" src="/api/file?url=<?= urlencode($post["image_path"]); ?>&type=image" alt="Card image cap" />
                    </div>
                    <?php endif; ?>
                    <div class="panel-footer">
                        <span>posted <?= $post["post_time"]; ?> by <?= e($post["username"]); ?></span> 
                    </div>
                </div>
                <!-- ./post -->
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <!-- ./feed -->
      </div>
      <div class="col-md-3">
        <!-- friends -->
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>Friends</h4>
                <div id="friends-div">
                    <?php if (count($this->data["data"]["friends"]) === 0): ?>
                        <p><?= e($this->data["data"]["username"]); ?> has absolutely no friends.</p>
                    <?php else: ?>
                        <ul>
                        <?php foreach ($this->data["data"]["friends"] as $friend): ?>
                            <li>
                                <a href="/profile?id=<?= $friend["id"]; ?>"><?= e($friend["username"]); ?></a> 
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
