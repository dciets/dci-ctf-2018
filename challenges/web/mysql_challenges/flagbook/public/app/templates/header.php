<!-- nav -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">Flagbook</a>
        </div>
    <?php if (isset($_SESSION["user_id"])): ?>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/">Home</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><a href="/messenger">Messenger</a></li>
            <li><a href="/logout">Logout</a></li>
        </ul>
    <?php endif; ?>
    </div>
</nav>
<!-- ./nav -->