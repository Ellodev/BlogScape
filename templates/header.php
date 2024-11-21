<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="home">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="navbar-item" href="write">
                <i class="fas fa-pencil-alt"></i> Write
            </a>
            <a class="navbar-item" href="other-blogs">
                <i class="fas fa-blog"></i> Other Blogs
            </a>
        </div>
        <div class="navbar-end">
            <div class="buttons">
                <div class="navbar-item">
                    <?php if (!isset($_SESSION['loggedin'])): ?>
                        <a class="button" href="login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a class="button is-primary" href="register">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php else: ?>
                        <a class="button is-warning" href="profile">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a class="button is-danger" href="logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
