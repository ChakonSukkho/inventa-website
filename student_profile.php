<?php

require_once "includes/init.php";
require_login();
require_role(['student']);

include "includes/header.php";

/* =========================
   Get Student Profile
========================= */
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn,
    "SELECT * FROM students WHERE user_id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

?>

<style>
    /* Tetapan Tema Rasmi */
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
    }

    .page-header {
        color: var(--gov-navy);
        font-weight: bold;
        border-bottom: 3px solid var(--gov-gold);
        padding-bottom: 10px;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Kad Rasmi */
    .card-gov {
        background-color: #ffffff;
        border-radius: 6px;
        border: 1px solid #dce1e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .card-gov-header {
        background-color: var(--gov-navy);
        color: #ffffff;
        padding: 15px 20px;
        font-weight: bold;
        border-bottom: 3px solid var(--gov-gold);
        display: flex;
        justify-content: space-between;
        align-items: center;
        letter-spacing: 1px;
    }

    .card-gov-body {
        padding: 25px;
    }

    /* Butang Tema */
    .btn-gov {
        background-color: var(--gov-navy);
        color: #ffffff;
        font-weight: bold;
        border: none;
        border-radius: 4px;
        padding: 8px 16px;
        letter-spacing: 0.5px;
        transition: background-color 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-gov:hover {
        background-color: #001a38;
        color: #ffffff;
    }

    .btn-gov-outline {
        background-color: transparent;
        color: var(--gov-navy);
        border: 2px solid var(--gov-navy);
        font-weight: bold;
        border-radius: 4px;
        padding: 6px 14px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-gov-outline:hover {
        background-color: var(--gov-navy);
        color: #ffffff;
    }

    /* Gaya Profil Gambar & Teks */
    .profile-img-container {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 4px solid var(--gov-navy);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        object-fit: cover;
        background-color: #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #666;
        margin: 0 auto;
    }

    .info-label {
        font-weight: bold;
        color: #555;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 1.05rem;
        color: var(--text-main);
        margin-bottom: 18px;
        border-bottom: 1px dashed #cccccc;
        padding-bottom: 6px;
    }

    /* Kad Bakat */
    .talent-card {
        border: 1px solid #dce1e6;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        height: 100%;
        overflow: hidden;
        background: #ffffff;
    }

    .cert-main-wrapper {
        background-color: #f0f3f5;
        border-bottom: 1px solid #dce1e6;
        padding: 10px;
        text-align: center;
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cert-main-img {
        max-height: 100%;
        max-width: 100%;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .badge-gov {
        background-color: var(--gov-navy);
        color: #fff;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: normal;
        font-size: 0.8rem;
    }

    .level-text {
        color: #555555;
        font-weight: bold;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
</style>

<div class="container mt-5 mb-5">

    <h3 class="page-header">My Profile</h3>

    <?php if (!$student): ?>

        <div class="alert alert-danger shadow-sm border-0" style="border-left: 4px solid #dc3545;">
            <strong>Error:</strong> Profile not found. Please contact administrator.
        </div>

    <?php else: ?>

        <div class="card-gov">
            <div class="card-gov-header">
                <span>PERSONAL INFORMATION</span>
                <a href="student_edit_profile.php" class="btn btn-light btn-sm text-dark fw-bold" style="border-radius: 4px;">
                    Edit Profile
                </a>
            </div>
            <div class="card-gov-body">
                <div class="row align-items-center">
                    
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        <?php if (!empty($student['profile_pic'])): ?>
                            <img src="uploads/<?= e($student['profile_pic']) ?>" alt="Profile Picture" class="profile-img-container">
                        <?php else: ?>
                            <div class="profile-img-container">No Image</div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?= e($student['student_name'] ?? 'N/A') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Matric Number</div>
                                <div class="info-value"><?= e($student['matric_no'] ?? 'N/A') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Program</div>
                                <div class="info-value"><?= e($student['program'] ?? 'N/A') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Year / Session</div>
                                <div class="info-value"><?= e($student['year_level'] ?? 'N/A') ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?= e($student['email'] ?? 'N/A') ?></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-end mb-3 mt-5">
            <h4 class="mb-0 text-uppercase fw-bold" style="color: var(--gov-navy); border-left: 4px solid var(--gov-gold); padding-left: 10px;">
                Talents & Certificates
            </h4>
            <a href="student_talents.php" class="btn-gov-outline">Manage Talents</a>
        </div>

        <div class="card-gov">
            <div class="card-gov-body bg-light">
                
                <?php
                $stmt2 = mysqli_prepare($conn,
                    "SELECT c.category_name, t.achievement, t.level,
                            t.certificate, t.certificate2, t.certificate3
                     FROM talents t
                     JOIN categories c ON t.category_id = c.category_id
                     WHERE t.student_id = ?"
                );

                mysqli_stmt_bind_param($stmt2, "i", $student['student_id']);
                mysqli_stmt_execute($stmt2);
                $talents = mysqli_stmt_get_result($stmt2);
                ?>

                <?php if ($talents && mysqli_num_rows($talents) > 0): ?>

                    <div class="row">
                        <?php while ($t = mysqli_fetch_assoc($talents)): ?>
                            <div class="col-md-6 mb-4">
                                <div class="talent-card">

                                    <?php
$images = [
    $t['certificate'],
    $t['certificate2'],
    $t['certificate3']
];

$mainImage = '';
foreach ($images as $img) {
    if (!empty($img)) {
        $mainImage = trim($img);
        break;
    }
}

$fileExt = '';
if (!empty($mainImage)) {
    $fileExt = strtolower(pathinfo($mainImage, PATHINFO_EXTENSION));
}
?>

<?php if (!empty($mainImage)): ?>
    <div class="cert-main-wrapper">
        <?php if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
            <img src="uploads/<?= e($mainImage); ?>" class="cert-main-img" alt="Certificate Image">
        <?php elseif ($fileExt === 'pdf'): ?>
            <embed src="uploads/<?= e($mainImage); ?>" type="application/pdf" width="100%" height="140px">
        <?php else: ?>
            <span class="text-muted">File available</span>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="cert-main-wrapper text-muted">
        <span>No Certificate Provided</span>
    </div>
<?php endif; ?>

                                    <div class="p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="badge-gov"><?= e($t['category_name'] ?? 'N/A') ?></span>
                                            <span class="level-text"><?= e($t['level'] ?? 'N/A') ?></span>
                                        </div>

                                        <h6 class="fw-bold mb-4" style="font-size: 1.05rem; color: #222; line-height: 1.4;">
                                            <?= e($t['achievement'] ?? 'N/A') ?>
                                        </h6>

                                        <?php if (!empty($mainImage)): ?>
                                            <a href="uploads/<?= e($mainImage); ?>"
                                               target="_blank"
                                               class="btn-gov w-100 text-center">
                                               View Certificate
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                <?php else: ?>
                    <div class="alert alert-info border-0 shadow-sm text-center py-4" style="background-color: #e6f2ff; color: var(--gov-navy);">
                        <p class="mb-2"><strong>No talents recorded yet.</strong></p>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Showcase your achievements by adding them to your profile.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>