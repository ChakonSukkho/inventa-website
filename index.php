<?php
require_once "includes/init.php";

if (!is_logged_in()) {
    header("Location: /login.php");
    exit;
}

switch ($_SESSION['role']) {
    case 'admin':
    case 'staff':
        header("Location: dashboard.php");
        exit;

    case 'student':
        header("Location: student_profile.php");
        exit;

    default:
        session_destroy();
        header("Location: /login.php");
        exit;
}