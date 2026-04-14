<?php
require_once "includes/init.php";
require_login();

if (!isset($_GET['id'])) {
    header("Location: student_talents.php");
    exit;
}

$talent_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. First, find out the student_id belonging to the logged in user
$stmt_s = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt_s, "i", $user_id);
mysqli_stmt_execute($stmt_s);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_s));

if (!$student) die("Access Denied.");

$student_id = $student['student_id'];

// 2. Find the talent and verify it belongs to THIS student
$stmt_t = mysqli_prepare($conn, "SELECT certificate, certificate2, certificate3 FROM talents WHERE talent_id = ? AND student_id = ?");
mysqli_stmt_bind_param($stmt_t, "ii", $talent_id, $student_id);
mysqli_stmt_execute($stmt_t);
$talent = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_t));

if ($talent) {
    // Cleanup files from server
    $files = [$talent['certificate'], $talent['certificate2'], $talent['certificate3']];
    foreach ($files as $f) {
        if (!empty($f) && file_exists("uploads/" . $f)) {
            unlink("uploads/" . $f);
        }
    }

    // Delete from DB
    $stmt_del = mysqli_prepare($conn, "DELETE FROM talents WHERE talent_id = ? AND student_id = ?");
    mysqli_stmt_bind_param($stmt_del, "ii", $talent_id, $student_id);
    mysqli_stmt_execute($stmt_del);

    header("Location: student_talents.php?msg=deleted");
    exit;
} else {
    die("Error: Record not found or you do not have permission to delete it.");
}
?>