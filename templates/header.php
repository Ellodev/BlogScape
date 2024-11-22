<?php if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require('templates/database.php');

$db = connectToDatabase();

if (isset($_SESSION['loggedin'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
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
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                if (!empty($user['profile_picture'])) {
                                    $profilePicturePath = $user['profile_picture'];
                            }   else {
                                    $profilePicturePath = 'uploads/default-avatar-light.png';
                            }
                            }
                            ?>
                            <figure class="image is-24x24" style="overflow: hidden">
                                <img src="<?= htmlspecialchars($profilePicturePath) ?>"
                                     alt="Profile Picture"
                                     style="object-fit: cover; border-radius: 50%; width: 100%; height: 100%">
                            </figure><?= htmlspecialchars($_SESSION['username'])?>
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
