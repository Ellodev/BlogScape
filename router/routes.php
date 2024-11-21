<?php
$page = basename($_SERVER['REQUEST_URI'] ?? '');

$viewDir = "views";

switch ($page) {
    case 'home':
    case 'index':
        require $viewDir . '/home.php';
        break;
    case 'write':
        require $viewDir . '/write.php';
        break;
    case 'posts':
        require $viewDir . '/posts.php';
        break;
    case 'login':
        require $viewDir . '/login.php';
        break;
    case 'register':
        require $viewDir . '/register.php';
        break;
    case 'logout':
        require $viewDir . '/logout.php';
        break;
    case 'change_pw':
        require $viewDir . '/change_pw.php';
        break;
    case 'other-blogs':
        require $viewDir . '/other-blogs.php';
        break;
    case 'profile':
        require $viewDir . '/profile.php';
        break;
    default:
        http_response_code(404);
        require $viewDir . '/404_view.php';
}
?>