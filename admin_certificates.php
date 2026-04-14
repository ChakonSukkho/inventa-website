<?php
require_once "includes/init.php";
require_login();
require_role(['admin','staff']);
include "includes/header.php";

/* =========================
   FILTERS LOGIC
========================= */
$search_matric = $_GET['matric'] ?? '';
$selected_categories = $_GET['category'] ?? []; // Array of categories
$selected_level = $_GET['level'] ?? '';

$sql = "SELECT s.student_name, s.matric_no, t.*, c.category_name 
        FROM talents t 
        JOIN students s ON t.student_id = s.student_id 
        JOIN categories c ON t.category_id = c.category_id 
        WHERE 1=1 ";

// Dynamic SQL for Multiple Categories
if (!empty($selected_categories)) {
    // Pastikan data selamat dari SQL Injection
    $ids = implode(',', array_map('intval', $selected_categories));
    $sql .= " AND t.category_id IN ($ids) ";
}

if (!empty($search_matric)) {
    $sql .= " AND s.matric_no LIKE '%" . mysqli_real_escape_string($conn, $search_matric) . "%' ";
}

if (!empty($selected_level)) {
    $sql .= " AND t.level = '" . mysqli_real_escape_string($conn, $selected_level) . "' ";
}

$sql .= " ORDER BY t.talent_id DESC";
$result = mysqli_query($conn, $sql);
$category_list = mysqli_query($conn, "SELECT * FROM categories");
?>

<style>
    /* Tema Rasmi & Warna */
    :root {
        --gov-navy: #002b5e;
        --gov-gold: #eeb012;
    }

    body { background-color: #f5f7fa !important; }
    .page-title { color: var(--gov-navy); font-weight: bold; border-left: 5px solid var(--gov-gold); padding-left: 15px; text-transform: uppercase; }
    .card-gov { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .btn-gov { background-color: var(--gov-navy); color: #ffffff; font-weight: bold; }
    .btn-gov:hover { background-color: #001a38; color: #ffffff; }
    
    /* Kad Sijil */
    .cert-card { transition: 0.3s; height: 100%; }
    .cert-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }
    .img-frame { height: 180px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .img-frame img { max-height: 100%; transition: transform 0.3s ease; }
    .cert-card:hover .img-frame img { transform: scale(1.05); }
    
    /* Dropdown Checkbox Styling */
    .dropdown-menu-custom {
        max-height: 250px;
        overflow-y: auto;
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        border: 1px solid #dce1e6;
    }
    .form-check-input:checked {
        background-color: var(--gov-navy);
        border-color: var(--gov-navy);
    }
    .btn-dropdown-select {
        background-color: #fff;
        border: 1px solid #ced4da;
        color: #333;
        text-align: left;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;
    }
    .btn-dropdown-select:focus {
        border-color: var(--gov-navy);
        box-shadow: 0 0 0 3px rgba(0, 43, 94, 0.15);
    }
    .filter-label {
        font-size: 0.75rem;
        font-weight: bold;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }
</style>

<div class="container mt-4 mb-5">
    <h3 class="page-title mb-4">Certificate Verification Center</h3>

    <div class="card-gov p-4 mb-4">
        <form method="GET" class="row g-3">
            
            <div class="col-md-3">
                <label class="filter-label">MATRIC NO</label>
                <input type="text" name="matric" class="form-control" value="<?= htmlspecialchars($search_matric) ?>" placeholder="Search Matric...">
            </div>

            <div class="col-md-4">
                <label class="filter-label">TALENT CATEGORIES</label>
                <div class="dropdown" style="height: calc(1.5em + .75rem + 2px);">
                    <button class="btn btn-dropdown-select w-100" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <span id="selected-text">All Categories</span>
                        <i class="fas fa-chevron-down ms-2" style="font-size: 0.8rem; color: #666;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-custom w-100 p-2" aria-labelledby="dropdownMenuButton">
                        <?php while($cat = mysqli_fetch_assoc($category_list)): ?>
                            <li class="px-2 py-1">
                                <div class="form-check">
                                    <input class="form-check-input category-checkbox" type="checkbox" 
                                           name="category[]" 
                                           value="<?= $cat['category_id'] ?>" 
                                           id="cat_<?= $cat['category_id'] ?>"
                                           <?= in_array($cat['category_id'], $selected_categories) ? 'checked' : '' ?>>
                                    <label class="form-check-label w-100" style="cursor: pointer; user-select: none;" for="cat_<?= $cat['category_id'] ?>">
                                        <?= htmlspecialchars($cat['category_name']) ?>
                                    </label>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <div class="col-md-2">
                <label class="filter-label">LEVEL</label>
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    <option value="University" <?= ($selected_level == 'University') ? 'selected' : '' ?>>University</option>
                    <option value="State" <?= ($selected_level == 'State') ? 'selected' : '' ?>>State</option>
                    <option value="National" <?= ($selected_level == 'National') ? 'selected' : '' ?>>National</option>
                    <option value="International" <?= ($selected_level == 'International') ? 'selected' : '' ?>>International</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100 shadow-sm">
                    <button type="submit" class="btn btn-gov">FILTER</button>
                    <a href="admin_certificates.php" class="btn btn-light border" style="color: #555; font-weight: bold; padding-top: 10px;">RESET</a>
                </div>
            </div>
            
        </form>
    </div>

    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card-gov cert-card">
                    <div class="img-frame border-bottom">
                        <?php if($row['certificate']): ?>
                            <img src="uploads/<?= $row['certificate'] ?>">
                        <?php else: ?>
                            <span class="text-muted small">No Document</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary"><?= $row['category_name'] ?></span>
                            <span class="small fw-bold text-muted"><?= strtoupper($row['level']) ?></span>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="min-height: 40px;"><?= htmlspecialchars($row['achievement']) ?></h6>
                        <div class="small text-muted border-top pt-2">
                            Student: <strong><?= htmlspecialchars($row['student_name']) ?></strong><br>
                            Matric No: <?= htmlspecialchars($row['matric_no']) ?>
                        </div>
                        <?php if($row['certificate']): ?>
                            <a href="uploads/<?= $row['certificate'] ?>" target="_blank" class="btn btn-dark btn-sm w-100 mt-3 fw-bold">View Full Document</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5 text-muted">
                <div style="font-size: 3rem; color: #dce1e6; margin-bottom: 15px;">
                    <i class="fas fa-search"></i>
                </div>
                <h5 class="fw-bold text-secondary">No Records Found</h5>
                <p>No achievement records match your current filter criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.category-checkbox');
    const selectedText = document.getElementById('selected-text');

    function updateDropdownText() {
        let checkedCount = 0;
        checkboxes.forEach(function(cb) {
            if (cb.checked) checkedCount++;
        });

        if (checkedCount === 0) {
            selectedText.textContent = 'All Categories';
        } else if (checkedCount === 1) {
            selectedText.textContent = '1 Category Selected';
        } else {
            selectedText.textContent = checkedCount + ' Categories Selected';
        }
    }

    // Kemas kini apabila checkbox ditekan
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateDropdownText);
    });

    // Kemas kini secara automatik semasa halaman dimuat
    updateDropdownText();
});
</script>

<?php include "includes/footer.php"; ?>