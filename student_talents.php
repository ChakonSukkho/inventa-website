<?php

require_once "includes/init.php";
require_login();
require_role(['student']);

include "includes/header.php";

/* =========================
   Get Student ID
========================= */
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn,
    "SELECT student_id FROM students WHERE user_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student data not found.");
}

$student_id = $student['student_id'];

/* =========================
   PAGINATION LOGIC
========================= */
$limit = 6;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* Get Total for Pagination */
$stmtTotal = mysqli_prepare($conn,
    "SELECT COUNT(*) AS total FROM talents WHERE student_id = ?"
);
mysqli_stmt_bind_param($stmtTotal, "i", $student_id);
mysqli_stmt_execute($stmtTotal);
$totalRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtTotal));
$total_pages = ceil($totalRow['total'] / $limit);

/* =========================
   GET TALENTS
========================= */
$stmt2 = mysqli_prepare($conn,
    "SELECT t.talent_id, c.category_name, 
            t.achievement, t.level, 
            t.certificate, t.certificate2, t.certificate3
     FROM talents t
     JOIN categories c ON t.category_id = c.category_id
     WHERE t.student_id = ?
     ORDER BY t.talent_id DESC
     LIMIT ? OFFSET ?"
);

mysqli_stmt_bind_param($stmt2, "iii", $student_id, $limit, $offset);
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
?>

<style>
    /* Force Theme Alignment */
    body {
        background-color: #f5f7fa !important;
    }

    .page-title {
        color: #002b5e;
        font-weight: bold;
        border-left: 5px solid #eeb012;
        padding-left: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Main Card & Talent Cards */
    .card-gov {
        background-color: #ffffff;
        border-radius: 8px;
        border: 1px solid #dce1e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .talent-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .talent-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .cert-preview-area {
        height: 160px;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #edf2f7;
        padding: 10px;
    }

    .cert-preview-area img {
        max-height: 100%;
        max-width: 100%;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Badges & Buttons */
    .badge-category {
        background-color: #002b5e;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.75rem;
        padding: 5px 10px;
    }

    .level-tag {
        font-size: 0.75rem;
        font-weight: bold;
        color: #666;
        text-transform: uppercase;
    }

    .btn-gov {
        background-color: #002b5e;
        color: #ffffff;
        font-weight: bold;
    }

    .btn-gov:hover {
        background-color: #001a38;
        color: #ffffff;
    }

    /* Pagination Styling */
    .pagination .page-link {
        color: #002b5e;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #002b5e;
        border-color: #002b5e;
    }
</style>

<div class="container mt-4 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="page-title">My Talents</h3>
        <a href="student_add_talent.php" class="btn btn-gov shadow-sm px-4">
            <i class="fas fa-plus me-2"></i>+ Add New Talent
        </a>
    </div>

    <div class="card-gov p-4">

        <?php if (mysqli_num_rows($result2) > 0): ?>

            <div class="row">
                <?php while ($talent = mysqli_fetch_assoc($result2)): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="talent-item-card shadow-sm">

                            <?php
                            $images = [$talent['certificate'], $talent['certificate2'], $talent['certificate3']];
                            $displayImg = '';
                            foreach ($images as $img) {
                                if (!empty($img)) { $displayImg = $img; break; }
                            }
                            ?>

                            <div class="cert-preview-area">
                                <?php if (!empty($displayImg)): ?>
                                    <img src="uploads/<?= e($displayImg); ?>" alt="Certificate">
                                <?php else: ?>
                                    <span class="text-muted small italic">No Image Uploaded</span>
                                <?php endif; ?>
                            </div>

                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge badge-category"><?= e($talent['category_name']); ?></span>
                                    <span class="level-tag"><?= e($talent['level']); ?></span>
                                </div>

                                <h6 class="fw-bold mb-3" style="min-height: 40px; color: #333;">
                                    <?= e($talent['achievement']); ?>
                                </h6>

                                <div class="border-top pt-3 d-flex justify-content-between">
                                    <div class="btn-group w-100">
                                        <a href="student_edit_talent.php?id=<?= $talent['talent_id']; ?>" 
                                           class="btn btn-outline-warning btn-sm fw-bold">Edit</a>
                                        
                                        <a href="student_delete_talent.php?id=<?= $talent['talent_id']; ?>" 
                                           class="btn btn-outline-danger btn-sm fw-bold"
                                           onclick="return confirm('Are you sure you want to delete this talent record?')">Delete</a>
                                    </div>
                                </div>

                                <?php if (!empty($displayImg)): ?>
                                    <a href="uploads/<?= e($displayImg); ?>" target="_blank" class="btn btn-gov btn-sm w-100 mt-2">
                                        View Certificate
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Prev</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted mb-3">You have not recorded any talents yet.</p>
                <a href="student_add_talent.php" class="btn btn-gov">+ Record First Talent</a>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include "includes/footer.php"; ?>