<?php
$page = $this->data["data"];
$form = $page["form"];
?>
<div class="row">
    <div class="col-md-12">
        <!-- edit profile -->
        <div class="panel panel-default">
            <div class="panel-body">
                <h4><?= $page["form_title"]; ?></h4>
                <form method="post" action="" id="info_form">
                    <?php if ($_GET["part"] < 3): ?>
                        <?php foreach ($form as $k => $value): ?>
                            <?php if (in_array($k, $page["fields"])): ?>
                                <p><?= $value[0]; ?></p>
                                <div class="form-group">
                                    <input class="form-control" type="text" name="<?= $k; ?>" value="<?= $value[1]; ?>">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>
                            Thank you for your valuable information. Make sure everything is okay before sending it.
                            This goes directly to Zark Muckerberg, exactly the way it's presented to you right now, so make sure it's perfect!
                        </p>
                        <hr/>
                        <?php foreach ($form as $k => $value): ?>
                            <?php if (in_array($k, $page["fields"])): ?>
                                <p><?= $value[0]; ?></p>
                                <div class="form-group">
                                    <input class="form-control" readonly type="text" value="<?= $value[1]; ?>">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <?php if ($page["submit_value_prev"] !== ""): ?>
                            <input id="back-btn" class="btn btn-warning" type="submit" name="back" src="<?= $page['prev']; ?>" value="<?= $page['submit_value_prev']; ?>">
                        <?php endif; ?>
                        <input id="next-btn" class="btn btn-primary" type="submit" name="send_info" src="<?= $page["next"]; ?>" value="<?= $page['submit_value_next']; ?>">
                    </div>
                </form>
            </div>
        </div>
        <!-- ./edit profile -->
    </div>
</div>
<script>
$('#back-btn').click(function(event) {
    $('#info_form').attr('action', $('#back-btn').attr('src'))
});

$('#next-btn').click(function(event) {
    $('#info_form').attr('action', $('#next-btn').attr('src'))
});
</script>
