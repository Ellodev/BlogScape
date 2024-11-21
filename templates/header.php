<?php if (session_status() == PHP_SESSION_NONE) {
session_start();
}
?>
<header>
    <nav class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="home">home</a>
            <a class="navbar-item" href="write">write</a>
        </div>
        <div class="navbar-end">
            <div class="buttons">
                <div class="buttons">
                    <div class="navbar-item">
                        <?php if (!isset($_SESSION['loggedin'])): ?>
                            <a class="button" href="login"><strong>login</strong></a>
                            <a class="button is-primary" href="register"><strong>register</strong></a>
                        <?php else: ?>
                            <a class="button is-warning" href="change_pw">change password</a>
                            <a class="button is-danger" href="logout"><strong>logout</strong></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>