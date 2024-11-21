<?php require "templates/header.php" ?>
<?php require "templates/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SESSION['loggedin'] !== true) {
    echo "You must be logged in to view this page.";
    exit;
} else {
    $db = connectToDatabase();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // CSRF token validation
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed.');
        }

        $error = "You're logged in";
        $title = $_POST['title'];
        $text = $_POST['text'];
        $image = $_POST['image'];

        // Input sanitization
        $title = sanitizeInput($title);
        $text = sanitizeInput($text);
        $image = validateURL($image);

        if ($title === false || $text === false || $image === false) {
            $error = "Invalid input.";
        } else {
            $stmt = $db->prepare("INSERT INTO posts (title, content, image, user_id) VALUES (:title, :text, :image, :user_id)");
            $stmt->execute([':title' => $title, ':text' => $text, ':image' => $image, ':user_id' => $_SESSION['user_id']]);
            header("Location: index");
            exit;
        }
    }
}

?>

<h1>Write a Post</h1>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>

<form method="POST" action="">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required value="<?php if (isset($title)) {echo "$title";} ?>">
    <br>
    <label for="text">Text:</label>
    <textarea name="text" id="text" required><?php if (isset($text)) {echo "$text";} ?></textarea>
    <br>
    <label for="image">Image URL:</label>
    <input type="url" name="image" id="image" required value="<?php if (isset($image)) {echo "$image";} ?>">
    <br>
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="submit" value="Submit">
</form>
