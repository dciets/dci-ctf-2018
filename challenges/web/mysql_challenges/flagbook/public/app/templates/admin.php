<?php
if (isset($_POST["sell_data"])) {
    $_SESSION["bank"] += 10000000;
}
?>
<?php if (!in_array($_SERVER['REMOTE_ADDR'], array("127.0.0.1", "::1"))): ?>
<div>Sorry, this section is only accessible from localhost.</div>
<?php else: ?>
<h1>Welcome, admin!</h1>
<div>
    <h3>Bank Balance: <?= number_format($_SESSION["bank"]); ?>$</h3>
    <form method="POST" action ="">
        <input type="submit" name="sell_data" value="Sell private users data" />
    </form>
</div>
<hr/>
<div>FLAG: DCI{24b9ed77e2385c65dbb6e5f911358e77}</div>
<?php endif;?>