<?php
require_once "includes/init.php";
require_login();
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $username   = trim($_POST['username']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm'];
    $department = trim($_POST['department']);

    if (empty($username) || empty($password) || empty($confirm) || empty($department)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($check))) {
            $error = "Username already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users(username, password, role, department) VALUES(?, ?, 'staff', ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $department);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Staff account created successfully!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

include "includes/header.php";
?>

<style>
    body { background-color: #f5f7fa !important; }
    .page-title { color: #002b5e; font-weight: bold; border-left: 5px solid #eeb012; padding-left: 15px; text-transform: uppercase; }
    .form-card { background-color: #ffffff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); max-width: 550px; margin: 0 auto; overflow: hidden; }
    .form-header { background-color: #002b5e; color: #ffffff; padding: 20px; text-align: center; font-weight: bold; border-bottom: 4px solid #eeb012; }
    .btn-gov { background-color: #002b5e; color: #ffffff; font-weight: bold; padding: 12px; }
    .btn-gov:hover { background-color: #001a38; color: #ffffff; }
</style>

<div class="container mt-4 mb-5">
    <h3 class="page-title mb-4">Add Staff Member</h3>

    <div class="form-card">
        <div class="form-header text-uppercase">Staff Registration Form</div>
        <div class="p-4">
            <?php if (isset($error)) echo "<div class='alert alert-danger text-center py-2 small'>$error</div>"; ?>
            <?php if (isset($success)) echo "<div class='alert alert-success text-center py-2 small'>$success</div>"; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter staff username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Department</label>
                    <select class="form-select" name="department" required>
                        <option value="">-- Select Department --</option>
                        <option value="JKA">JKA</option>
                        <option value="JKM">JKM</option>
                        <option value="JKE">JKE</option>
                        <option value="JTMK">JTMK</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-secondary">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small text-secondary">Confirm Password</label>
                        <input type="password" name="confirm" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gov w-100 mb-2">CREATE ACCOUNT</button>
                <a href="staff_list.php" class="btn btn-light border w-100 fw-bold text-secondary">BACK TO LIST</a>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>