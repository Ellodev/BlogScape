<?php require "templates/header.php"?>
<?php
require_once 'templates/database.php';
?>

<h1>posts</h1>

<?php
$db = new database();
$pdo = $db->getConnection();

$posts = $pdo->query('SELECT * FROM posts')->fetchAll();

foreach ($posts as $post) {
    echo "<h2>{$post['Title']}</h2>";
    echo "<p>{$post['Text']}</p>";
    echo '<img src="' . htmlspecialchars($post['Picture']) . '" alt="Post Image">';
    echo "<p>By {$post['user']} on {$post['date']} </p>";

}
?>