<?php
require_once "includes/init.php";

if($_SERVER['REQUEST_METHOD']=="POST"){

    // CAPTCHA CHECK (ADDED)
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';

    if (empty($recaptcha)) {
        $error = "Please verify that you are not a robot.";
    } else {

        $secret = "6LcsZ7csAAAAAGLQNehX35uEstRKWEyasjVjPpNg"; //replace with your secret key

        $response = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha"
        );

        $responseKeys = json_decode($response, true);

        if (!$responseKeys["success"]) {
            $error = "CAPTCHA verification failed. Try again.";
        } else {

            // ORIGINAL LOGIN LOGIC (UNCHANGED)
            $username=$_POST['username'];
            $password=$_POST['password'];

            $stmt=mysqli_prepare($conn,"SELECT * FROM users WHERE username=?");
            mysqli_stmt_bind_param($stmt,"s",$username);
            mysqli_stmt_execute($stmt);
            $result=mysqli_stmt_get_result($stmt);
            $user=mysqli_fetch_assoc($result);

            if($user && password_verify($password,$user['password'])){
                $_SESSION['user_id']=$user['user_id'];
                $_SESSION['username']=$user['username'];
                $_SESSION['role']=$user['role'];
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error="Invalid Username or Password."; 
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
<title>Login - INVENTA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- CAPTCHA SCRIPT -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
    /* Tetapan Warna Korporat Rasmi */
    :root {
        --gov-blue: #003366;   
        --gov-gold: #FFD700;   
        --gov-bg: #F0F4F8;     
        --text-dark: #333333;
    }

    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: var(--gov-bg);
        font-family: 'Arial', sans-serif;
    }

    .navbar-custom {
        background-color: #ffffff;
        border-bottom: 4px solid var(--gov-blue);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        padding: 10px 20px;
    }

    .navbar-brand {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .system-logo {
        height: 50px;
        width: auto;
        margin-right: 15px;
        object-fit: contain;
    }

    .system-title {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .system-title .main-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--gov-blue);
        letter-spacing: 1.5px;
        line-height: 1;
        margin-bottom: 2px;
    }

    .system-title .sub-name {
        font-size: 0.75rem;
        color: #666;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .content-wrapper {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-card {
        width: 100%;
        max-width: 420px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        overflow: hidden;
        border: 1px solid #dce1e6;
    }

    .login-header {
        background-color: var(--gov-blue);
        color: white;
        padding: 25px 20px;
        text-align: center;
        border-bottom: 4px solid var(--gov-gold);
    }

    .login-header h5 {
        margin: 0;
        font-weight: bold;
        font-size: 1.2rem;
        letter-spacing: 1px;
    }

    .login-body {
        padding: 35px 30px;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
    }

    .form-control {
        border-radius: 4px;
        padding: 10px 15px;
        border: 1px solid #ced4da;
    }

    .form-control:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.2);
    }

    .btn-login {
        background-color: var(--gov-blue);
        color: white;
        font-weight: bold;
        padding: 12px;
        border-radius: 4px;
        border: none;
        letter-spacing: 0.5px;
        transition: 0.3s;
    }

    .btn-login:hover {
        background-color: #002244;
        color: white;
    }

    .link-register {
        color: var(--gov-blue);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .link-register:hover {
        text-decoration: underline;
    }

    .footer-custom {
        background-color: #ffffff;
        border-top: 1px solid #e5e5e5;
        padding: 15px;
        text-align: center;
        font-size: 0.8rem;
        color: #666;
    }
</style>
</head>
<body>

<nav class="navbar navbar-custom">
    <div class="container-fluid">
        <div class="navbar-brand">
            <img src="logo.png" alt="INVENTA Logo" class="system-logo" onerror="this.src='https://via.placeholder.com/50x50/003366/FFFFFF?text=I'">
            
            <div class="system-title">
                <span class="main-name">INVENTA</span>
                <span class="sub-name">Student Talent Management System</span>
            </div>
        </div>
    </div>
</nav>

<div class="content-wrapper">
    <div class="login-card">
        
        <div class="login-header">
            <h5>USER LOGIN</h5>
        </div>

        <div class="login-body">
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger py-2 text-center" style="font-size: 0.85rem; font-weight:600;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="mb-3">
                    <label class="form-label">Id Number</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter Id Number" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()" style="border-color: #ced4da;">
                            👁
                        </button>
                    </div>
                </div>

                <!-- CAPTCHA UI -->
                <div class="mb-4">
                    <div class="g-recaptcha" data-sitekey="6LcsZ7csAAAAAOg0HYnLqTQh_yaHG7tOD4TDHhOl"></div>
                </div>

                <button type="submit" class="btn btn-login w-100 mb-4">LOGIN</button>
            </form>
        </div>
    </div>
</div>

<div class="footer-custom">
    &copy; <?= date("Y") ?> All Rights Reserved. INVENTA System.
</div>

<script>
function togglePassword(){
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>