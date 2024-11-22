<?php require "templates/header.php" ?>
<?php require "templates/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['loggedin'])) {
    echo "You must be logged in to write a post.";
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

<h1 class="is-size-1 has-text-centered title">write a post</h1>

<div class="block is-flex is-justify-content-center is-align-items-center">

<?php
if (isset($error)) {
    echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
}
?>

<form method="POST" action="" style="min-width: 300px">
    <div class="field">
        <label for="title" class="label">title</label>
        <div class="control">
            <input type="text" name="title" class="input" required value="<?php if (isset($title)) {echo "$title";} ?>">
        </div>
    </div>
    <div class="field">
        <label for="text" class="label">text</label>
        <div class="control">
            <textarea name="text" class="textarea is-normal" id="text" required><?php if (isset($text)) {echo "$text";} ?></textarea>
        </div>
    </div>
    <div class="field">
        <label class="label" for="image">image url</label>
        <div class="control">
            <input type="url" class="input is-url" name="image" id="image" required value="<?php if (isset($image)) {echo "$image";} ?>">
        </div>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input class="button is-primary" type="submit" value="Submit">
</form>

</div>