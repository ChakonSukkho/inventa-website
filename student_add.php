<?php
require_once "includes/init.php";
require_login();

/* ============================================================
   ACCESS CONTROL
   Both Admin and Staff are allowed to register new students.
============================================================ */
require_role(['admin', 'staff']);

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Account Details
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    // Student Personal Details
    $name    = trim($_POST['student_name']);
    $matric  = trim($_POST['matric_no']);
    $program = trim($_POST['program']);
    $year    = (int)trim($_POST['year_level']); 
    $email   = trim($_POST['email']);

    /* =========================
       VALIDATION
    ========================= */
    if (empty($username) || empty($password) || empty($confirm) || empty($name) || empty($matric)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // 1. Check if Username already exists
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if (mysqli_fetch_assoc($result)) {
            $error = "Username already exists. Please choose another.";
        } else {
            // 2. Check if Matric Number already exists
            $checkMatric = mysqli_prepare($conn, "SELECT student_id FROM students WHERE matric_no = ?");
            mysqli_stmt_bind_param($checkMatric, "s", $matric);
            mysqli_stmt_execute($checkMatric);
            $resultMatric = mysqli_stmt_get_result($checkMatric);

            if (mysqli_fetch_assoc($resultMatric)) {
                $error = "This matric number is already registered.";
            } else {
                /* =========================
                   PROCESS REGISTRATION
                ========================= */
                mysqli_begin_transaction($conn);

                try {
                    // A. Insert into USERS table
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_user = mysqli_prepare($conn, "INSERT INTO users(username, password, role) VALUES(?, ?, 'student')");
                    mysqli_stmt_bind_param($stmt_user, "ss", $username, $hash);
                    mysqli_stmt_execute($stmt_user);
                    
                    $new_user_id = mysqli_insert_id($conn);

                    // B. Insert into STUDENTS table
                    $stmt_student = mysqli_prepare($conn, "INSERT INTO students(user_id, student_name, matric_no, program, year_level, email) VALUES(?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmt_student, "isssis", $new_user_id, $name, $matric, $program, $year, $email);
                    mysqli_stmt_execute($stmt_student);

                    mysqli_commit($conn);
                    $success = "Student account successfully created!";

                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error = "Registration failed: " . $e->getMessage();
                }
            }
        }
    }
}

include "includes/header.php";
?>

<style>
    /* Official INVENTA Theme Styling */
    :root {
        --gov-navy: #002b5e;
        --gov-gold: #eeb012;
    }

    body { background-color: #f5f7fa !important; }

    .page-title { 
        color: var(--gov-navy); 
        font-weight: bold; 
        border-left: 5px solid var(--gov-gold); 
        padding-left: 15px; 
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-card { 
        background-color: #ffffff; 
        border-radius: 8px; 
        border: 1px solid #dce1e6; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.06); 
        max-width: 700px; 
        margin: 0 auto; 
        overflow: hidden; 
    }

    .form-header { 
        background-color: var(--gov-navy); 
        color: #ffffff; 
        padding: 20px; 
        text-align: center; 
        font-weight: bold; 
        border-bottom: 4px solid var(--gov-gold); 
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .section-label { 
        font-size: 0.8rem; 
        font-weight: bold; 
        color: var(--gov-navy); 
        text-transform: uppercase; 
        margin-bottom: 15px; 
        display: block; 
        border-bottom: 1px solid #eee; 
        padding-bottom: 5px; 
    }

    .btn-gov { 
        background-color: var(--gov-navy); 
        color: #ffffff; 
        font-weight: bold; 
        padding: 12px; 
    }

    .btn-gov:hover {
        background-color: #001a38;
        color: #ffffff;
    }

    .form-label { font-weight: 600; color: #555; }
</style>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="page-title">Enroll New Student</h3>
        <a href="student_list.php" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="form-card">
        <div class="form-header">Student Registration Portal</div>
        <div class="p-4">
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center py-2 fw-bold" style="font-size: 0.85rem;"><?= $error ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center py-2 fw-bold" style="font-size: 0.85rem;"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                
                <span class="section-label">Personal Information</span>
                
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="student_name" class="form-control" placeholder="Enter full name as per Identity Card" required value="<?= isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : '' ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Matric Number</label>
                        <input type="text" name="matric_no" class="form-control" placeholder="e.g. 13DIT23F2006" required value="<?= isset($_POST['matric_no']) ? htmlspecialchars($_POST['matric_no']) : '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program (Department)</label>
                        <select class="form-select" name="program" required>
                            <option value="JTMK" <?= (isset($_POST['program']) && $_POST['program'] == 'JTMK') ? 'selected' : '' ?>>JTMK</option>
                            <option value="JKA" <?= (isset($_POST['program']) && $_POST['program'] == 'JKA') ? 'selected' : '' ?>>JKA</option>
                            <option value="JKM" <?= (isset($_POST['program']) && $_POST['program'] == 'JKM') ? 'selected' : '' ?>>JKM</option>
                            <option value="JKE" <?= (isset($_POST['program']) && $_POST['program'] == 'JKE') ? 'selected' : '' ?>>JKE</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Current Semester / Year</label>
                        <input type="number" name="year_level" class="form-control" placeholder="e.g. 5" required value="<?= isset($_POST['year_level']) ? htmlspecialchars($_POST['year_level']) : '' ?>">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Official Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="student@email.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>

                <span class="section-label">Account Security</span>
                
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Set a unique username for login" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Create password" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm" class="form-control" placeholder="Re-type password" required>
                    </div>
                </div>

                <hr class="mb-4">

                <button type="submit" class="btn btn-gov w-100">REGISTER STUDENT ACCOUNT</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>