<?php
require_once "includes/init.php";
require_login();

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $new_password = $_POST['new_password'];
    $confirm      = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm) {
        $message = "Passwords do not match.";
    } else {

        $hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "
            UPDATE users 
            SET password = ?, must_change_password = 0 
            WHERE user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "si", $hash, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Failed to update password.";
        }
    }
}

include "includes/header.php";
?>

<div class="container mt-5" style="max-width:500px;">
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3 text-center">Change Your Password</h4>

        <div class="alert alert-warning text-center small">
            You must change your password before continuing.
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label fw-bold">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold">
                Update Password
            </button>

        </form>
    </div>
</div>

<?php include "includes/footer.php"; ?>