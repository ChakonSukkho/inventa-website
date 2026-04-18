<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "includes/init.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    $name      = trim($_POST['student_name']);
    $matric    = trim($_POST['matric_no']);
    $program   = trim($_POST['program']);
    $year      = trim($_POST['year_level']);
    $email     = trim($_POST['email']);

    /* =========================
       VALIDATION
    ========================= */
    if (
        empty($username) ||
        empty($password) ||
        empty($confirm) ||
        empty($name) ||
        empty($matric) ||
        empty($program) ||
        empty($year) ||
        empty($email)
    ) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        /* =========================
           CHECK USERNAME
        ========================= */
        $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $username);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if (mysqli_fetch_assoc($result)) {
            $error = "Username already exists.";
        } else {

            /* =========================
               CHECK MATRIC NUMBER
            ========================= */
            $checkMatric = mysqli_prepare($conn, "SELECT student_id FROM students WHERE matric_no = ?");
            mysqli_stmt_bind_param($checkMatric, "s", $matric);
            mysqli_stmt_execute($checkMatric);
            $resultMatric = mysqli_stmt_get_result($checkMatric);

            if (mysqli_fetch_assoc($resultMatric)) {
                $error = "This matric number is already used.";
            } else {

                /* =========================
                   INSERT INTO USERS
                ========================= */
                $hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
                mysqli_stmt_bind_param($stmt, "ss", $username, $hash);

                if (mysqli_stmt_execute($stmt)) {

                    // Get new user_id
                    $user_id = mysqli_insert_id($conn);

                    /* =========================
                       INSERT INTO STUDENTS
                    ========================= */
                    $stmt2 = mysqli_prepare(
                        $conn,
                        "INSERT INTO students (user_id, student_name, matric_no, program, year_level, email)
                         VALUES (?, ?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt2,
                        "isssis",
                        $user_id,
                        $name,
                        $matric,
                        $program,
                        $year,
                        $email
                    );

                    if (mysqli_stmt_execute($stmt2)) {
                        $success = "Registration successful. You can login now.";
                    } else {
                        $error = "Student insert failed: " . mysqli_stmt_error($stmt2);
                    }

                } else {
                    $error = "User insert failed: " . mysqli_stmt_error($stmt);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - INVENTA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo.png">
    
    <style>
        :root {
            --gov-navy: #002b5e;
            --gov-gold: #eeb012;
            --gov-bg: #f5f7fa;
            --text-main: #333333;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--gov-bg);
            font-family: Arial, Helvetica, sans-serif;
        }

        /* --- NAVBAR --- */
        .navbar-gov {
            background-color: #ffffff;
            border-bottom: 4px solid var(--gov-navy);
            padding: 12px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .navbar-brand-gov {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-logo {
            height: 45px;
            margin-right: 15px;
        }

        .sys-title .main-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--gov-navy);
            letter-spacing: 1px;
            line-height: 1.1;
            display: block;
        }

        .sys-title .sub-title {
            font-size: 0.75rem;
            color: #555555;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* --- CONTENT WRAPPER --- */
        .main-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .signup-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 550px;
            border-radius: 6px;
            border: 1px solid #dce1e6;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .signup-header {
            background-color: var(--gov-navy);
            color: #ffffff;
            text-align: center;
            padding: 20px;
            border-bottom: 3px solid var(--gov-gold);
        }

        .signup-header h4 {
            margin: 0;
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 1.5px;
        }

        .signup-body {
            padding: 30px;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: bold;
            color: var(--gov-navy);
            margin-bottom: 15px;
            text-transform: uppercase;
            border-left: 4px solid var(--gov-gold);
            padding-left: 10px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .form-control, .form-select {
            border-radius: 4px;
            border: 1px solid #cccccc;
            padding: 8px 12px;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--gov-navy);
            box-shadow: 0 0 0 3px rgba(0, 43, 94, 0.1);
            outline: none;
        }

        .btn-gov {
            background-color: var(--gov-navy);
            color: #ffffff;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            padding: 12px;
            width: 100%;
            letter-spacing: 0.5px;
            transition: background-color 0.2s ease;
            margin-top: 10px;
        }

        .btn-gov:hover {
            background-color: #001a38;
            color: #ffffff;
        }

        .footer-gov {
            background-color: #ffffff;
            border-top: 1px solid #e0e0e0;
            padding: 15px;
            text-align: center;
            font-size: 0.75rem;
            color: #777777;
        }

        .login-link {
            color: var(--gov-navy);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="navbar-gov">
    <div class="container-fluid">
        <a href="login.php" class="navbar-brand-gov">
            <img src="logo.png" alt="Logo" class="nav-logo" onerror="this.src='https://via.placeholder.com/50x50/002b5e/ffffff?text=I'">
            <div class="sys-title">
                <span class="main-title">INVENTA</span>
                <span class="sub-title">Student Talent Management</span>
            </div>
        </a>
    </div>
</nav>

<div class="main-wrapper">
    <div class="signup-card">
        
        <div class="signup-header">
            <h4>STUDENT REGISTRATION</h4>
        </div>

        <div class="signup-body">
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger text-center py-2" style="font-size: 0.85rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center py-2" style="font-size: 0.85rem;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="section-title">Personal Information</div>
                
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="student_name" placeholder="Enter your full name" required value="<?= isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : '' ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Matric Number</label>
                        <input type="text" class="form-control" name="matric_no" placeholder="e.g. 13DDT21F1001" required value="<?= isset($_POST['matric_no']) ? htmlspecialchars($_POST['matric_no']) : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Program</label>
                        <select class="form-select" name="program" required>
                            <option value="">Select Program</option>
                            <option value="JKA" <?= (isset($_POST['program']) && $_POST['program'] == 'JKA') ? 'selected' : '' ?>>JKA</option>
                            <option value="JKM" <?= (isset($_POST['program']) && $_POST['program'] == 'JKM') ? 'selected' : '' ?>>JKM</option>
                            <option value="JKE" <?= (isset($_POST['program']) && $_POST['program'] == 'JKE') ? 'selected' : '' ?>>JKE</option>
                            <option value="JTMK" <?= (isset($_POST['program']) && $_POST['program'] == 'JTMK') ? 'selected' : '' ?>>JTMK</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Year Level</label>
                        <input type="number" class="form-control" name="year_level" placeholder="Current Semester" required value="<?= isset($_POST['year_level']) ? htmlspecialchars($_POST['year_level']) : '' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="example@email.com" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                </div>

                <div class="section-title mt-2">Account Security</div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">Id Number</label>
                        <input type="text" class="form-control" name="username" placeholder="Enter Id Number" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Create password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="confirm" placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="btn-gov">REGISTER ACCOUNT</button>
                
                <div class="text-center mt-4 pt-3" style="border-top: 1px solid #eee;">
                    <span style="font-size: 0.85rem; color: #666;">Already have an account?</span> 
                    <a href="login.php" class="login-link">Sign In Here</a>
                </div>

            </form>
        </div>
    </div>
</div>

<div class="footer-gov">
    &copy; <?= date("Y") ?> INVENTA System. All Rights Reserved.
</div>

</body>
</html>