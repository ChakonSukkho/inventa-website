<?php
require_once "includes/init.php";
require_login();

// ONLY Admin can delete staff
if ($_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have permission to delete staff members.");
}

if (!isset($_GET['id'])) {
    header("Location: staff_list.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    die("Error: You cannot delete your own admin account.");
}

// Check if user exists and is actually a staff member
$check = mysqli_query($conn, "SELECT role FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($check);

if ($user && $user['role'] === 'staff') {
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: staff_list.php?msg=staff_removed");
        exit;
    } else {
        die("Error deleting record: " . mysqli_error($conn));
    }
} else {
    die("Access Denied: Target user is not a staff member or does not exist.");
}
?>