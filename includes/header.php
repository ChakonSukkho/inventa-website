<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventa | Student Talent Management System</title>

    <meta name="description" content="Inventa is a student talent management system that helps manage student profiles, achievements, talents, and certificates in one platform.">
    <meta name="keywords" content="Inventa, student talent management, student certificates, student achievements, student profile system">
    <meta name="author" content="Inventa">
    <meta name="robots" content="noindex, follow">

    <meta property="og:title" content="Inventa | Student Talent Management System">
    <meta property="og:description" content="Manage student profiles, talents, achievements, and certificates in one platform.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://inventa.my">
    <meta property="og:image" content="https://inventa.my/logo.png">

    <link rel="icon" type="image/png" href="logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* TEMA RASMI INVENTA */
        :root {
            --gov-navy: #002b5e;
            --gov-gold: #eeb012;
            --gov-bg: #f5f7fa;
            --text-main: #333333;
        }

        body {
            background-color: var(--gov-bg);
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text-main);
            margin: 0;
            padding-bottom: 60px; /* Ruang untuk footer */
        }

        /* --- NAVBAR RASMI --- */
        .navbar-gov {
            background-color: #ffffff;
            border-bottom: 4px solid var(--gov-navy);
            padding: 10px 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
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

        .sys-title {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sys-title .main-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--gov-navy);
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .sys-title .sub-title {
            font-size: 0.75rem;
            color: #555555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* --- MENU NAVIGASI --- */
        .nav-link-gov {
            color: #555555;
            font-weight: 600;
            font-size: 0.9rem;
            margin-left: 15px;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .nav-link-gov:hover {
            color: var(--gov-navy);
            background-color: #f0f3f5;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 15px;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #bb2d3b;
            color: white;
        }

        .user-badge {
            background-color: var(--gov-navy);
            color: white;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            margin-left: 15px;
        }
    </style>
</head>
<body>

<nav class="navbar-gov d-flex justify-content-between align-items-center">
    
    <a href="index.php" class="navbar-brand-gov">
        <img src="logo.png" alt="Logo" class="nav-logo" onerror="this.src='https://via.placeholder.com/50x50/002b5e/ffffff?text=I'">
        <div class="sys-title">
            <span class="main-title">INVENTA</span>
            <span class="sub-title">Student Talent Management</span>
        </div>
    </a>

    <div class="d-flex align-items-center">
        
        <?php if (isset($_SESSION['role'])): ?>
            
            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
                <a href="dashboard.php" class="nav-link-gov">Dashboard</a>
                <a href="student_list.php" class="nav-link-gov">Students</a>
                <a href="admin_certificates.php" class="nav-link-gov">Certificates</a>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="staff_list.php" class="nav-link-gov">Staffs</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'student'): ?>
                <a href="student_profile.php" class="nav-link-gov">My Profile</a>
                <a href="student_talents.php" class="nav-link-gov">My Talents</a>
            <?php endif; ?>

            <span class="user-badge">
                <?= htmlspecialchars(strtoupper($_SESSION['username'] ?? 'USER')) ?> 
                (<?= htmlspecialchars(ucfirst($_SESSION['role'])) ?>)
            </span>
            <a href="logout.php" class="btn-logout">LOGOUT</a>

        <?php endif; ?>

    </div>
</nav>

<div class="container">