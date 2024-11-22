<?php require "templates/header.php" ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'templates/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['like'])) {
        $post_id = $_POST['post_id'];
        $db = connectToDatabase();
        $stmt = $db->prepare("INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)");
        $stmt->execute([':post_id' => $post_id , ':user_id' => $_SESSION['user_id']]);
    } else if (isset($_POST['comment'])) {
        $post_id = $_POST['post_id'];
        $comment = $_POST['comment'];
        $db = connectToDatabase();
        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment)");
        $stmt->execute([':post_id' => $post_id , ':user_id' => $_SESSION['user_id'], ':comment' => $comment]);
    }
}
?>

<h1>posts</h1>

<?php
$db = connectToDatabase();

$query = "
    SELECT posts.*, users.username
    FROM posts
    JOIN users ON posts.user_id = users.user_id
";

$posts = $db->query($query)->fetchAll();

foreach ($posts as $post) {
    $post_id = $post['post_id'];
    echo "<h2>{$post['title']}</h2>";
    echo "<p>{$post['content']}</p>";
    echo '<img src="' . htmlspecialchars($post['image']) . '" alt="Post Image">';
    echo "<p>By {$post['username']} on {$post['created_at']} </p>";
    echo "<form method='POST' action=''>
              <input type='hidden' name='post_id' value='{$post['post_id']}'>
              <button type='submit' name='like' class='like-button'>Like</button>
          </form>";
    echo "<p>Likes: " . $db->query("SELECT COUNT(*) FROM likes WHERE post_id = {$post['post_id']}")->fetchColumn() . "</p>";
    echo "<form method='POST' action=''>
              <input type='hidden' name='post_id' value='{$post['post_id']}'>
              <input type='text' name='comment' id='comment' placeholder='Comment'>
              <button type='submit' name='comment-button' class='comment-button'>Comment</button>
          </form>";
    $stmt = $db->prepare("
        SELECT comments.*, users.username
        FROM comments
        JOIN users ON comments.user_id = users.user_id
        WHERE comments.post_id = :post_id
    ");
    $stmt->execute([':post_id' => $post_id]);
    $comments = $stmt->fetchAll();

    foreach ($comments as $comment) {
        echo "<p>{$comment['comment_text']} - by {$comment['username']}</p>";
    }


}
?>