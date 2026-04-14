<?php
require_once "includes/init.php";
require_login();
require_role(['admin', 'staff']); // Hanya Admin & Staff boleh tengok senarai ni

/* =========================
   PAGINATION & SEARCH LOGIC
========================= */
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search_query = "";
if (!empty($search)) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $search_query = " AND (s.student_name LIKE '%$safe_search%' OR s.matric_no LIKE '%$safe_search%' OR s.program LIKE '%$safe_search%') ";
}

/* =========================
   SYARAT PENAPISAN ROLE
   Kita tambah u.role = 'student' supaya 
   akaun Admin/Staff tak muncul dalam list.
========================= */
$role_filter = " AND u.role = 'student' ";

// Dapatkan jumlah baris untuk pagination (dengan tapisan role)
$total_rows_query = "SELECT COUNT(*) as t 
                     FROM students s 
                     JOIN users u ON s.user_id = u.user_id 
                     WHERE 1=1 $role_filter $search_query";

$total_rows_result = mysqli_query($conn, $total_rows_query);
$total_rows = mysqli_fetch_assoc($total_rows_result)['t'];
$total_pages = ceil($total_rows / $limit);

// Ambil data (Hanya role 'student' sahaja)
$sql = "SELECT s.*, u.username 
        FROM students s 
        JOIN users u ON s.user_id = u.user_id 
        WHERE 1=1 $role_filter $search_query 
        ORDER BY s.student_name ASC 
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

include "includes/header.php";
?>

<style>
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
    .card-gov { 
        background: #fff; 
        border-radius: 8px; 
        border: 1px solid #dce1e6; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.06); 
    }
    .table thead { background-color: var(--gov-navy); color: #fff; }
    .pfp-sm { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 2px solid var(--gov-navy); }
    .btn-gov { background-color: var(--gov-navy); color: #ffffff; font-weight: bold; }
    .btn-gov:hover { background-color: #001a38; color: #ffffff; }
</style>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="page-title">Student Directory</h3>
        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff'): ?>
            <a href="student_add.php" class="btn btn-gov shadow-sm">
                <i class="fas fa-plus me-2"></i>Register New Student
            </a>
        <?php endif; ?>
    </div>

    <div class="card-gov p-3 mb-4 shadow-sm">
        <form method="GET" class="row g-2">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, matric no, or program..." 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3 text-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-dark fw-bold">SEARCH</button>
                    <a href="student_list.php" class="btn btn-outline-secondary">RESET</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-gov p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Student Details</th>
                        <th>Matric No</th>
                        <th>Program</th>
                        <th>Year</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <?php if(!empty($row['profile_pic'])): ?>
                                        <img src="uploads/<?= $row['profile_pic'] ?>" class="pfp-sm me-3">
                                    <?php else: ?>
                                        <div class="pfp-sm me-3 bg-light d-flex align-items-center justify-content-center text-muted border">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold" style="color: var(--gov-navy);"><?= htmlspecialchars($row['student_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($row['matric_no']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['program']) ?></span></td>
                            <td>Year <?= htmlspecialchars($row['year_level']) ?></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <a href="student_view.php?id=<?= $row['student_id'] ?>" 
                                       class="btn btn-outline-primary btn-sm px-3 fw-bold">View</a>

                                    <?php if ($_SESSION['role'] === 'admin'): ?>
                                        <a href="student_edit_profile.php?id=<?= $row['student_id'] ?>" 
                                           class="btn btn-outline-warning btn-sm px-3 fw-bold">Edit</a>
                                        <a href="student_delete.php?id=<?= $row['student_id'] ?>" 
                                           class="btn btn-outline-danger btn-sm px-3 fw-bold" 
                                           onclick="return confirm('Delete this student permanently?')">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No student records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if($total_pages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" style="color: var(--gov-navy);"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>