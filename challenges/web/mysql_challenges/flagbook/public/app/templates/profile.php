<div class="row">
      <div class="col-md-3">
        <!-- edit profile -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Edit profile</h4>
            <form method="post" action="">
              <p>Status:</p>
              <div class="form-group">
                <input class="form-control" type="text" name="status" placeholder="Status" value="<?= e($_SESSION["status"]); ?>">
              </div>
              <p>Location:</p>
              <div class="form-group">
                <input class="form-control" type="text" name="location" placeholder="Location" value="<?= e($_SESSION["location"]); ?>">
              </div>

              <div class="form-group">
                <input class="btn btn-primary" type="submit" name="update_profile" value="Save">
              </div>
            </form>
          </div>
        </div>
        <!-- ./edit profile -->
        <!-- edit profile -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Private information</h4>
            <p>Tell us more about yourself so that we can <!-- sell your data --> customize your experience on Flagbook.</p>
            <form method="POST" action="/editprofile?part=1">
                <input class="btn btn-primary" type="submit" name="update_private" value="OK, let's do it!">
            </form>
          </div>
        </div>
        <!-- ./edit profile -->
      </div>
      <div class="col-md-6">
        <!-- user profile -->
        <div class="media">
          <div class="media-left">
            <img src="/api/image?url=uploads/avatar.png" class="media-object" style="width: 128px; height: 128px;">
          </div>
          <div class="media-body">
            <h2 class="media-heading">Name: <?= e($_SESSION["username"]); ?></h2>
            <p>Status: <?= e($_SESSION["status"]); ?></p>
          </div>
        </div>
        <!-- user profile -->

        <hr>

        <!-- feed -->
        <div>
        <?php if (count($this->data["data"]["posts"]) === 0): ?>
          <p>You have not posted anything yet.</p>
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
                    <img class="card-img-top center-block" style="max-width: 100%; max-height: 300px;" src="/api/image?url=<?= urlencode($post["image_path"]); ?>" alt="Card image cap" />
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
                        <p>You have absolutely no friends.</p>
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