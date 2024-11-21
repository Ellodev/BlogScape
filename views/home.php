<?php require "templates/header.php"; ?>

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
    } elseif (isset($_POST['comment'])) {
        $post_id = $_POST['post_id'];
        $comment = $_POST['comment'];
        $db = connectToDatabase();
        $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment)");
        $stmt->execute([':post_id' => $post_id , ':user_id' => $_SESSION['user_id'], ':comment' => htmlspecialchars($comment)]);
    }
}
?>

<h1 class="is-size-1 has-text-centered">Posts</h1>

<div class="is-flex is-justify-content-center is-flex-direction-column is-align-items-center">
    <?php
    $db = connectToDatabase();

    $query = "
    SELECT posts.*, users.username
    FROM posts
    JOIN users ON posts.user_id = users.user_id
    ORDER BY posts.created_at DESC
    ";

    $posts = $db->query($query)->fetchAll();

    foreach ($posts as $post) {
        $post_id = $post['post_id'];
        $likesCount = $db->query("SELECT COUNT(*) FROM likes WHERE post_id = {$post_id}")->fetchColumn();
        $commentsQuery = "
        SELECT comments.*, users.username
        FROM comments
        JOIN users ON comments.user_id = users.user_id
        WHERE comments.post_id = :post_id
        ORDER BY comments.created_at DESC
        ";
        $stmt = $db->prepare($commentsQuery);
        $stmt->execute([':post_id' => $post_id]);
        $comments = $stmt->fetchAll();
        ?>
        <div class="box" style="max-width: 800px; width: 100%; margin-bottom: 20px;">
            <h3 class="is-size-3"><?= htmlspecialchars($post['title']) ?></h3>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-height: 300px; width: 100%; object-fit: cover;">
            <p class="mt-2"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
            <p><small>By <?= htmlspecialchars($post['username']) ?> on <?= $post['created_at'] ?></small></p>

            <!-- Like Button -->
            <form method="POST" action="" class="mt-2">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <button type="submit" name="like" class="button is-primary is-small">Like</button>
            </form>
            <p>Likes: <?= $likesCount ?></p>

            <!-- Comment Form -->
            <form method="POST" action="" class="mt-2">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="text" name="comment" id="comment" class="input is-small" placeholder="Add a comment...">
                <button type="submit" name="comment" class="button is-info is-small mt-1">Comment</button>
            </form>

            <!-- Comments Section -->
            <div class="section mt-3">
                <?php foreach ($comments as $comment): ?>
                    <div class="notification is-light">
                        <p><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php } ?>
</div>
