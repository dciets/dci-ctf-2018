<!DOCTYPE html>
<html>
    <?php include 'head.php'; ?>

    <body>
        <?php include 'header.php'; ?>
        <!-- main -->
        <main class="container">
            <?php /*
            <?php if (count($this->data["flash"]) >= 2): ?>
                <div class="alert alert-<?php echo $this->data["flash"][0]; ?>" role="alert">
                    <ul>
                    <?php foreach ($this->data["flash"][1] as $msg): ?>
                        <li><?php echo $msg; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            */ ?>
            <?php echo $this->data["main"]; ?>
    

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLongTitle">
                            <?php if ($this->data["status"] !== 1): ?>
                                Error(s)
                            <?php else: ?>
                                Success!
                            <?php endif; ?>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="myModalBody">
                        <ul>
                        <?php foreach ($this->data["messages"] as $msg): ?>
                            <li><?= $msg; ?></li>
                        <?php endforeach; ?>
                        </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- ./main -->
        <?php include 'footer.php'; ?>
    </body>
</html>
<?php if ($this->data["status"] !== -1): ?>
<script>
    $('#myModal').modal("show")
</script>
<?php endif; ?>