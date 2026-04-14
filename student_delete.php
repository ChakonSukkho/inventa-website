<?php
require_once "includes/init.php";
require_login();

/* =========================
   SECURITY CHECK
========================= */
// Only Admin is allowed to delete students
if ($_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have permission to perform this action.");
}

/* =========================
   VALIDATE STUDENT ID
========================= */
if (!isset($_GET['id'])) {
    header("Location: student_list.php");
    exit;
}

$student_id = (int)$_GET['id'];

// 1. Get student info to find user_id and profile_pic filename
$stmt = mysqli_prepare($conn, "SELECT user_id, profile_pic FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if ($student) {
    $user_id = $student['user_id'];
    $profile_pic = $student['profile_pic'];

    /* =========================
       FILE CLEANUP
    ========================= */
    // Delete profile picture from uploads folder if it exists
    if (!empty($profile_pic)) {
        $file_path = "uploads/" . $profile_pic;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    /* =========================
       DATABASE DELETION
    ========================= */
    // Start Transaction to ensure both deletions happen or none at all
    mysqli_begin_transaction($conn);

    try {
        // Delete from students table
        $del_student = mysqli_prepare($conn, "DELETE FROM students WHERE student_id = ?");
        mysqli_stmt_bind_param($del_student, "i", $student_id);
        mysqli_stmt_execute($del_student);

        // Delete from users table (since student profile is tied to a user account)
        $del_user = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($del_user, "i", $user_id);
        mysqli_stmt_execute($del_user);

        // If your database has "ON DELETE CASCADE" on foreign keys, 
        // deleting from 'users' might be enough, but this is safer.

        mysqli_commit($conn);
        
        // Redirect with success (You can add a session flash message here if your system supports it)
        header("Location: student_list.php?msg=deleted");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Error deleting record: " . $e->getMessage());
    }

} else {
    die("Student record not found.");
}
?>