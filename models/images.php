<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Maximum file size in bytes (e.g., 5MB)
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Allowed file types (Mime Types)
$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

require('../templates/database.php');
// Establish the database connection using PDO
$db = connectToDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    // Sanitize the username to prevent SQL injection
    $username = $_SESSION['username'];

    // Get the image file details
    $image = $_FILES['profile_picture'];
    $imageName = basename($image['name']);
    $imageTmp = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageType = mime_content_type($imageTmp);

    // Check if the uploaded file is an allowed image type
    if (!in_array($imageType, $allowedMimeTypes)) {
        die("Error: Only JPG, PNG, and GIF files are allowed.");
    }

    // Check file size (max 5MB)
    if ($imageSize > $maxFileSize) {
        die("Error: The file is too large. Maximum allowed size is 5MB.");
    }

    // Generate a unique filename to avoid overwriting and potential path traversal
    $targetDir = "../uploads/";
    $targetFile = $targetDir . uniqid('', true) . "_" . basename($imageName);

    // Move the image to the target directory
    if (move_uploaded_file($imageTmp, $targetFile)) {
        try {
            $databasePath = str_replace('../', '', $targetFile);
            // Prepare and bind the SQL query to update the profile picture in the database
            $stmt = $db->prepare("UPDATE users SET profile_picture = :profile_picture WHERE username = :username");
            $stmt->bindParam(':profile_picture', $databasePath);
            $stmt->bindParam(':username', $username);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['message'] = [
                    'content' => 'Profile picture updated successfully.',
                    'type' => 'success', // can be 'success', 'danger', 'info', or 'warning'
                ];
                header('Location: profile');
            } else {
                $_SESSION['message'] = [
                    'content' => 'Error updating profile picture.',
                    'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
                ];
                header('Location: profile');
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = [
                'content' => 'Error updating profile picture.',
                'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
            ];
            header('Location: profile');
        }
    } else {
        $_SESSION['message'] = [
            'content' => 'Error updating profile picture.',
            'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
        ];
        header('Location: profile');
    }
} else {
    $_SESSION['message'] = [
        'content' => 'Error updating profile picture.',
        'type' => 'danger', // can be 'success', 'danger', 'info', or 'warning'
    ];
    header('Location: profile');
}

?>
