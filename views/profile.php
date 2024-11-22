<?php
require "templates/header.php";
require "templates/database.php";
require 'templates/notification.php';

$db = connectToDatabase();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $_SESSION['username']]);
$user = $stmt->fetch();

if ($user && !empty($user['profile_picture'])) {
    $profilePicturePath = $user['profile_picture'];
} else {
    $profilePicturePath = 'uploads/default-avatar.png';  // You can use a default image
}

?>

<section class="section">
    <div class="container">
        <h1 class="title is-size-1 has-text-centered">Profile</h1>

        <div class="columns is-centered">
            <div class="column is-narrow">
                <figure class="image is-128x128 is-rounded">
                    <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture">
                </figure>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column is-half">
                <h2 class="subtitle is-size-3">Name: <?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
                <h2 class="subtitle is-size-3">Username: <?php echo $user['username']; ?></h2>
                <h2 class="subtitle is-size-3">Email: <?php echo $user['email']; ?></h2>
                <h2><a href="change_pw" class="button is-link is-light">Change Password</a></h2>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column is-half">
                <form method="post" enctype="multipart/form-data" action="models/images.php" class="box">
                    <div class="field">
                        <label class="label">Profile Picture</label>
                        <div class="control">
                            <input type="file" name="profile_picture" class="input" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">First Name</label>
                        <div class="control">
                            <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" class="input" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Last Name</label>
                        <div class="control">
                            <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" class="input" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="input" required>
                        </div>
                    </div>

                    <div class="field is-grouped is-grouped-centered">
                        <div class="control">
                            <button type="submit" class="button is-primary">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
