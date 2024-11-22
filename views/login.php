<?php require("templates/header.php"); ?>
<?php

require "templates/notification.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = validateEmail($_POST['email']);
    $password = $_POST['password'];

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }

    if (validateEmail($email) === false) {
        $error = "Invalid email.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id'];

                $_SESSION['message'] = [
                    'content' => 'Logged in successfully!',
                    'type' => 'success', // can be 'success', 'danger', 'info', or 'warning'
                ];

                header("Location: index");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2 class="has-text-centered is-size-2">Login</h2>

<?php
if (isset($error)) {
    $_SESSION['message'] = [
        'content' => htmlspecialchars($error), // Sanitize error message to prevent XSS
        'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
    ];
    header("Location: login");
    exit;
}
?>

<div class="block is-flex is-justify-content-center is-align-items-center">

    <form method="POST" action="login" style="min-width:400px">
        <div class="field">
            <label for="email" class="label">Email</label>
            <div class="control">
                <input type="email" name="email" id="email" class="input" required value="<?php if (isset($email)) {echo htmlspecialchars($email);} ?>">
            </div>
        </div>
        <div class="field">
            <label for="password" class="label">Password</label>
            <div class="control">
                <input type="password" name="password" id="password" class="input" required>
            </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Login</button>
            </div>
        </div>
    </form>

</div>

</body>
</html>
