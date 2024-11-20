<?php
function connectToDatabase()
{
    try {
        return new PDO('mysql:host=localhost;dbname=blog', 'root', '');
    } catch (PDOException $e) {
        die('Cant connect to Database.: ' . $e->getMessage());
    }
}

function validateEmail($email) {
    $email = sanitizeInput($email);
    if ($email === false) {
        return false;
    } else {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

function validatePassword($password) {
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasSpecialChar = preg_match('/[\W_]/', $password);

    if ($hasLowercase && $hasUppercase && $hasSpecialChar && strlen($password) >= 8) {
        return true;
    }
    return false;
}


function sanitizeInput($input) {
    $input = trim($input);
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

function validateURL($url) {
    sanitizeInput($url);
    if ($url === false) {
        return false;
    } else {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}
