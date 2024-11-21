<?php
function connectToDatabase()
{
    try {
        return new PDO('mysql:host=localhost;dbname=blog', 'root', '');
    } catch (PDOException $e) {
        die('Cant connect to Database.: ' . $e->getMessage());
    }
}

function connectToSecondDatabase()
{
    require_once('envParser.php'); // Ensure envParser is loaded
    loadEnv(__DIR__ . '/.env'); // Load environment variables

    // Use environment variables to configure connection
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_DATABASE'] ?? 'test';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    try {
        return new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die('Cant connect to second database: ' . $e->getMessage());
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
