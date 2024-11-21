<?php
require('templates/header.php');
require('templates/database.php');

$db = connectToSecondDatabase();

$query = "SELECT blog_url, blog_von
FROM blogs
WHERE jahr = 2024";

$blogs = $db->query($query)->fetchAll();

?>

<h1 class="is-size-1 has-text-centered">Other Blogs</h1>

<div class="is-flex is-justify-content-center is-flex-direction-column is-align-items-center">
    <?php
    foreach ($blogs as $blog) {
       echo "<a href='{$blog['blog_url']}' target='_blank'>{$blog['blog_von']}</a>";
    }?>
