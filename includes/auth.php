<?php

/* =========================
   LOGIN CHECK
========================= */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

/* =========================
   ROLE CHECK
========================= */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === "admin";
}

function is_staff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === "staff";
}

function is_student() {
    return isset($_SESSION['role']) && $_SESSION['role'] === "student";
}

/* =========================
   GET USER DEPARTMENT / PROGRAM
========================= */
function get_user_department() {
    global $conn;

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        return null;
    }

    $user_id = $_SESSION['user_id'];
    $role    = $_SESSION['role'];

    // Staff table does not exist in current database
    if ($role === 'staff') {
        return 'Staff';
    }

    // Student uses "program", not "department"
    if ($role === 'student') {
        $stmt = mysqli_prepare($conn,
            "SELECT program FROM students WHERE user_id = ? LIMIT 1"
        );

        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['program'] ?? null;
    }

    return null;
}

/* =========================
   ROLE PROTECTION
========================= */
function require_role($roles = []) {

    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit;
    }

    if (!in_array($_SESSION['role'], $roles)) {

        if ($_SESSION['role'] === 'student') {
            header("Location: student_profile.php");
        } else {
            header("Location: dashboard.php");
        }

        exit;
    }
}

?>