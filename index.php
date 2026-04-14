<?php
if ($_SERVER['HTTP_HOST'] === 'www.inventa.my' || empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header("Location: https://inventa.my" . $_SERVER['REQUEST_URI']);
    exit;
}

require_once "includes/init.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

switch ($_SESSION['role']) {
    case 'admin':
    case 'staff':
        header("Location: dashboard.php");
        break;

    case 'student':
        header("Location: student_profile.php");
        break;

    default:
        session_destroy();
        header("Location: login.php");
}

exit;