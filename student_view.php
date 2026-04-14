<?php
require_once "includes/init.php";
require_login();
require_role(['admin', 'staff']);

if (!isset($_GET['id'])) {
    header("Location: student_list.php");
    exit;
}

$student_id = (int)$_GET['id'];

// Get Student Data
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$student) die("Student not found.");

// Get Student Talents
$stmt2 = mysqli_prepare($conn, "
    SELECT t.*, c.category_name 
    FROM talents t 
    JOIN categories c ON t.category_id = c.category_id 
    WHERE t.student_id = ?
");
mysqli_stmt_bind_param($stmt2, "i", $student_id);
mysqli_stmt_execute($stmt2);
$talents = mysqli_stmt_get_result($stmt2);

include "includes/header.php";
?>

<style>
    body { background-color: #f5f7fa !important; }
    .page-title { color: #002b5e; font-weight: bold; border-left: 5px solid #eeb012; padding-left: 15px; text-transform: uppercase; }
    .card-gov { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); margin-bottom: 25px; }
    .info-label { color: #6c757d; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; }
    .info-value { color: #002b5e; font-size: 1rem; font-weight: 600; border-bottom: 1px dashed #dee2e6; padding-bottom: 5px; margin-bottom: 15px; }
    .profile-img { width: 130px; height: 130px; object-fit: cover; border-radius: 50%; border: 4px solid #002b5e; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .cert-thumb { height: 100px; width: 100%; object-fit: cover; border-radius: 4px; border: 1px solid #eee; }
</style>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="page-title">Student Detailed Profile</h3>
        <a href="student_list.php" class="btn btn-outline-secondary fw-bold px-4">Back to List</a>
    </div>

    <div class="card-gov p-4">
        <div class="row align-items-center">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <?php if ($student['profile_pic']): ?>
                    <img src="uploads/<?= $student['profile_pic'] ?>" class="profile-img">
                <?php else: ?>
                    <div class="profile-img d-flex align-items-center justify-content-center bg-light mx-auto" style="color: #ccc; font-weight: bold;">NO IMAGE</div>
                <?php endif; ?>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-12">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= htmlspecialchars($student['student_name']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Matric Number</div>
                        <div class="info-value"><?= htmlspecialchars($student['matric_no']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?= htmlspecialchars($student['email']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Program</div>
                        <div class="info-value"><?= htmlspecialchars($student['program']) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Current Year</div>
                        <div class="info-value">Year <?= htmlspecialchars($student['year_level']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h4 class="fw-bold mb-3" style="color: #002b5e;">ACHIEVEMENTS & CERTIFICATES</h4>
    <div class="row">
        <?php if (mysqli_num_rows($talents) > 0): ?>
            <?php while($t = mysqli_fetch_assoc($talents)): ?>
                <div class="col-md-6 mb-3">
                    <div class="card-gov p-3 h-100 d-flex flex-row align-items-center">
                        <div style="width: 100px; flex-shrink: 0;" class="me-3">
                            <?php if($t['certificate']): ?>
                                <img src="uploads/<?= $t['certificate'] ?>" class="cert-thumb">
                            <?php else: ?>
                                <div class="cert-thumb bg-light d-flex align-items-center justify-content-center small">No File</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge bg-primary mb-1"><?= $t['category_name'] ?></span>
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($t['achievement']) ?></h6>
                            <small class="text-muted text-uppercase fw-bold"><?= $t['level'] ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-4 bg-white rounded border">
                <p class="text-muted mb-0">No achievements recorded for this student yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "includes/footer.php"; ?>