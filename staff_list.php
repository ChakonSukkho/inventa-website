<?php
require_once "includes/init.php";
require_login();
require_role(['admin']); // Only Admin can manage staff

include "includes/header.php";

/* =========================
   FETCH STAFF DATA
========================= */
$result = mysqli_query($conn,
"SELECT u.user_id, u.username, u.department, u.created_at
 FROM users u
 WHERE u.role='staff'
 ORDER BY u.user_id DESC");
?>

<style>
    body { background-color: #f5f7fa !important; }
    .page-title { color: #002b5e; font-weight: bold; border-left: 5px solid #eeb012; padding-left: 15px; text-transform: uppercase; letter-spacing: 1px; }
    .card-gov { background-color: #ffffff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .table-gov thead { background-color: #002b5e; color: #ffffff; }
    .badge-dept { background-color: #eeb012; color: #000; font-weight: bold; }
    .btn-gov { background-color: #002b5e; color: #ffffff; font-weight: bold; }
    .btn-gov:hover { background-color: #001a38; color: #ffffff; }
</style>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="page-title">Staff Management</h3>

        <!-- BUTTON SECTION -->
        <div class="d-flex gap-2">
            
            <a href="admin_import_students.php" class="btn btn-gov shadow-sm">
                <i class="fas fa-file-import me-2"></i>Import Staff
            </a>

            <a href="staff_add.php" class="btn btn-gov shadow-sm">
                <i class="fas fa-user-plus me-2"></i>+ Add New Staff
            </a>

        </div>
    </div>

    <div class="card-gov p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">Username</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-navy"><?= htmlspecialchars($row['username']) ?></td>
                            <td>
                                <span class="badge badge-dept">
                                    <?= htmlspecialchars($row['department'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><span class="text-muted">Staff</span></td>
                            <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    <a href="staff_edit.php?id=<?= $row['user_id'] ?>" 
                                       class="btn btn-outline-warning btn-sm px-3 fw-bold">
                                       Edit
                                    </a>
                                    <a href="staff_delete.php?id=<?= $row['user_id'] ?>" 
                                       class="btn btn-outline-danger btn-sm px-3 fw-bold"
                                       onclick="return confirm('Are you sure you want to remove this staff member?')">
                                       Remove
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No staff records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>