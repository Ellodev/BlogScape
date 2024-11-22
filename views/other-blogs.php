<?php
require('templates/header.php');

$db = connectToSecondDatabase();

$query = "SELECT blog_url, blog_von
FROM blogs
WHERE jahr = 2024";

$blogs = $db->query($query)->fetchAll();

?>

<h1 class="is-size-1 has-text-centered title">other blogs</h1>

<div class="is-flex is-justify-content-center is-flex-direction-column is-align-items-center">
    <?php
    foreach ($blogs as $blog) {
        $blogURL = htmlspecialchars($blog['blog_url']);
        $blogVon = htmlspecialchars($blog['blog_von']);

       echo "<a href='{$blogURL}' target='_blank'>{$blogVon}</a>";
    }?>
