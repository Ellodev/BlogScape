<?php require "templates/header.php"?>
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


require('templates/database.php');
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt -> execute(['username' => $username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($old_password, $user['password'])) {
        if ($new_password !== $confirm_password) {
            $error = "Passwords do not match!";
        } elseif (validatePassword($new_password) === false) {
            $error = "Password must be at least 8 characters long, contain a uppercase and lowercase letter and a special character!";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_ARGON2ID);

            $stmt = $db->prepare("UPDATE users SET password = :password WHERE username = :username");
            $stmt->execute([':password' => $hashed_password, ':username' => $username]);

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
    <title>Register</title>
</head>
<body>

<h2>Register</h2>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>

<form method="POST" action="">
    <label for="old_password">Old Password:</label>
    <input type="password" name="old_password" id="old_password" required>
    <br>
    <label for="new_password">New Password:</label>
    <input type="password" name="new_password" id="new_password" required>
    <br>
    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" required>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <br>

    <button type="submit">Register</button>
</form>

<p>Already have an account? <a href="login">Login here</a></p>

</body>
</html>
