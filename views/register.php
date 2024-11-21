<?php require "templates/header.php"?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


require('templates/database.php');
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed.');
    }


    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (validateEmail($email) === false) {
        $error = "Invalid email!";
    } elseif(validatePassword($password) === false) {
        $error = "Password must be at least 8 characters long, contain a uppercase and lowercase letter and a special character!";
    } elseif (strlen($username) < 4 && sanitizeInput($username) === false) {
        $error = "Username must be at least 4 characters long!";
    } elseif (sanitizeInput($firstname) === false || sanitizeInput($lastname) === false) {
        $error = "First name and last name must not contain special characters!";
    }
    else {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() > 0) {
            $error = "Username already taken!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_ARGON2ID);

            $stmt = $db->prepare("INSERT INTO users (username, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email, $firstname, $lastname]);

            header("Location: login");
            exit;
        }
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
<h2 class="has-text-centered is-size-2">Register</h2>

<div class="block is-flex is-justify-content-center is-align-items-center is-flex-direction-column">

<form method="POST" action="" style="min-width: 400px">
    <div class="field">
        <label for="firstname" class="label">First Name</label>
        <div class="control">
            <input type="text" name="firstname" id="firstname" class="input" required value="<?php if (isset($firstname)) {echo "$firstname";} ?>">
        </div>
    </div>
    <div class="field">
        <label for="lastname" class="label">Last Name</label>
        <div class="control">
            <input type="text" name="lastname" id="lastname" class="input" required value="<?php if (isset($lastname)) {echo "$lastname";} ?>">
        </div>
    </div>
    <div class="field">
        <label for="username" class="label">Username</label>
        <div class="control">
            <input type="text" name="username" id="username" class="input" required value="<?php if (isset($username)) {echo "$username";} ?>">
        </div>
    </div>
    <div class="field">
        <label for="email" class="label">Email</label>
        <div class="control">
            <input type="email" name="email" id="email" class="input" required value="<?php if (isset($email)) {echo "$email";} ?>">
        </div>
    </div>
    <div class="field">
        <label for="password" class="label">Password</label>
        <div class="control">
            <input type="password" name="password" id="password" class="input" required>
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
            <button type="submit" class="button is-primary is-centered">Register</button>
        </div>
    </div>
</form>
    <p>Already have an account? <a href="login">Login here</a></p>
</div>



</body>
</html>
