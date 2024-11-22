<?php
require "templates/header.php";

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
    $profilePicturePath = 'uploads/default-avatar-light.png';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $firstname = sanitizeInput($_POST['firstname']);
    $lastname = sanitizeInput($_POST['lastname']);
    $email = validateEmail($_POST['email']);

    if ($email === false) {
        $error = "Invalid email.";
    } else {
        $stmt = $db->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname, email = :email WHERE username = :username");
        $stmt->execute([':firstname' => $firstname, ':lastname' => $lastname, ':email' => $email, ':username' => $_SESSION['username']]);

        $_SESSION['message'] = [
            'content' => 'Profile updated successfully.',
            'type' => 'success', // can be 'success', 'danger', 'info', or 'warning'
        ];
    }
}

$stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $_SESSION['username']]);
$user = $stmt->fetch();
?>

<section class="section">
    <div class="container">
        <h1 class="title is-size-1 has-text-centered">Profile</h1>

        <div class="columns is-centered">
            <div class="column is-narrow">
                <figure class="image is-128x128" style="overflow: hidden">
                    <img src="<?php echo htmlspecialchars($profilePicturePath); ?>" alt="Profile Picture" style="object-fit: cover; border-radius: 50%; width: 100%; height: 100%">
                </figure>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column is-half">
                <h2 class="subtitle is-size-3">Name: <?php echo $user['firstname'] . ' ' . htmlspecialchars($user['lastname']); ?></h2>
                <h2 class="subtitle is-size-3">Username: <?php echo $user['username']; ?></h2>
                <h2 class="subtitle is-size-3">Email: <?php echo $user['email']; ?></h2>
                <h2><a href="change_pw" class="button is-link is-light">Change Password</a></h2>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column is-half">
                <h2 class="subtitle is-size-3">Stats</h2>
                <?php
                // Get statistics
                $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $user['user_id']]);
                $posts = $stmt->fetch();

                $stmt = $db->prepare("SELECT COUNT(*) FROM likes WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $user['user_id']]);
                $likes = $stmt->fetch();

                $stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE user_id = :user_id");
                $stmt->execute([':user_id' => $user['user_id']]);
                $comments = $stmt->fetch();

                echo "<h2 class='subtitle is-size-4'>Posts: " . htmlspecialchars($posts[0]['COUNT(*)']) . "</h2>";
                echo "<h2 class='subtitle is-size-4'>Likes: " . htmlspecialchars($likes[0]['COUNT(*)']) . "</h2>";
                echo "<h2 class='subtitle is-size-4'>Comments: " . htmlspecialchars($comments[0]['COUNT(*)']) . "</h2>";
                ?>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column is-half">
                <form method="post" enctype="multipart/form-data" action="models/images.php" class="box">
                    <div class="field">
                        <label class="label">Profile Picture</label>
                        <div class="control">
                            <input type="file" name="profile_picture" class="input">
                        </div>
                    </div>
                    <div class="field is-grouped is-grouped-centered">
                        <div class="control">
                            <button type="submit" class="button is-primary">Update Profile Picture</button>
                        </div>
                    </div>
                </form>

                <form method="post" action="">
                    <div class="field">
                        <label class="label">First Name</label>
                        <div class="control">
                            <input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" class="input" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Last Name</label>
                        <div class="control">
                            <input type="text" name="lastname" value="<?php echo $user['lastname']; ?>" class="input" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Email</label>
                        <div class="control">
                            <input type="email" name="email" value="<?php echo $user['email']; ?>" class="input" required>
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
