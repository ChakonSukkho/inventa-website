<?php
require_once "includes/init.php";
require_login();
require_role(['admin']);

$dept_result = mysqli_query($conn, "SELECT * FROM departments ORDER BY department_name ASC");

if (!isset($_GET['id'])) {
    header("Location: staff_list.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Fetch Current Staff Data
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ? AND role = 'staff'");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$staff = mysqli_fetch_assoc($result);

if (!$staff) {
    die("Staff member not found.");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username   = trim($_POST['username']);
    $department = trim($_POST['department']);
    $new_password = $_POST['new_password'];

    if (empty($username) || empty($department)) {
        $error = "Username and Department are required.";
    } else {
        // Update basic info
        if (!empty($new_password)) {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt_upd = mysqli_prepare($conn, "UPDATE users SET username = ?, department = ?, password = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt_upd, "sssi", $username, $department, $hash, $user_id);
        } else {
            $stmt_upd = mysqli_prepare($conn, "UPDATE users SET username = ?, department = ? WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt_upd, "ssi", $username, $department, $user_id);
        }

        if (mysqli_stmt_execute($stmt_upd)) {
            header("Location: staff_list.php?msg=updated");
            exit;
        } else {
            $error = "Update failed: " . mysqli_error($conn);
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
    <h3 class="page-title mb-4">Edit Staff Profile</h3>

    <div class="form-card">
        <div class="form-header text-uppercase">Update Staff Information</div>
        <div class="p-4">
            <?php if (isset($error)) echo "<div class='alert alert-danger text-center py-2 small'>$error</div>"; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($staff['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Department</label>
                    <select class="form-select" name="department" required>

                        <?php while($dept = mysqli_fetch_assoc($dept_result)): ?>
                            <option value="<?= $dept['department_name'] ?>"
                                <?= ($staff['department'] == $dept['department_name']) ? 'selected' : '' ?>>
                                <?= $dept['department_name'] ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">New Password (Optional)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="Leave blank to keep current password">
                    <small class="text-muted">Only fill this if you want to reset the staff's password.</small>
                </div>

                <button type="submit" class="btn btn-gov w-100 mb-2">SAVE CHANGES</button>
                <a href="staff_list.php" class="btn btn-light border w-100 fw-bold text-secondary">CANCEL</a>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>