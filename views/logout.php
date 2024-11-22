<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_destroy();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['message'] = [
    'content' => 'Logged out successfully!',
    'type' => 'success', // can be 'success', 'danger', 'info', or 'warning'
];

header('Location: login');
exit;

