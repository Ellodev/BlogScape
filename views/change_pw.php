<?php require "templates/header.php";
require "templates/notification.php";?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($old_password, $user['password'])) {
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
        } elseif (validatePassword($new_password) === false) {
            $error = "Password must be at least 8 characters long, contain a uppercase and lowercase letter, and a special character!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);

            $stmt = $db->prepare("UPDATE users SET password = :password WHERE username = :username");
            $stmt->execute([':password' => $hashed_password, ':username' => $username]);
            $_SESSION['message'] = [
                'content' => 'Password changed successfully.',
                'type' => 'success', // can be 'success', 'danger', 'info', or 'warning'
            ];

            header('Location: index');
            exit;
        }
    } else {
        $error = "Invalid password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>

<h2 class="has-text-centered is-size-2">Change Password</h2>

<?php
if (isset($error)) {
    $_SESSION['message'] = [
        'content' => $error,
        'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
    ];
}
?>

<div class="block is-flex is-justify-content-center is-align-items-center">
    <form method="POST" action="" style="min-width:400px">
        <div class="field">
            <label for="old_password" class="label">Old Password</label>
            <div class="control">
                <input type="password" name="old_password" id="old_password" class="input" required>
            </div>
        </div>
        <div class="field">
            <label for="new_password" class="label">New Password</label>
            <div class="control">
                <input type="password" name="new_password" id="new_password" class="input" required>
            </div>
        </div>
        <div class="field">
            <label for="confirm_password" class="label">Confirm Password</label>
            <div class="control">
                <input type="password" name="confirm_password" id="confirm_password" class="input" required>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="field">
            <div class="control">
                <button class="button is-primary" type="submit">Submit</button>
            </div>
        </div>
    </form>
</div>

</body>
</html>
