<?php
require_once "includes/init.php";
require_login();
require_role(['admin','staff']);

include "includes/header.php";

/* =========================
   DASHBOARD FILTERS (COMBINED LOGIC)
========================= */
$filter_program = $_GET['program'] ?? '';
$filter_cat     = $_GET['cat_filter'] ?? '';   // Filter for Category
$filter_level   = $_GET['level_filter'] ?? ''; // Filter for Level

// Bina Global SQL supaya semua penapis digunakan pada semua graf
$global_sql = "";
if (!empty($filter_program)) {
    $global_sql .= " AND s.program = '" . mysqli_real_escape_string($conn, $filter_program) . "' ";
}
if (!empty($filter_cat)) {
    $global_sql .= " AND t.category_id = '" . mysqli_real_escape_string($conn, $filter_cat) . "' ";
}
if (!empty($filter_level)) {
    $global_sql .= " AND t.level = '" . mysqli_real_escape_string($conn, $filter_level) . "' ";
}

/* =========================
   STATISTICS QUERIES
========================= */
// 1. Total Students (Filtered)
$totalStudent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT s.student_id) AS t FROM students s JOIN talents t ON s.student_id = t.student_id WHERE 1=1 $global_sql"))['t'];

// 2. Data for Category Chart
$catDataQuery = "
    SELECT c.category_name, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN categories c ON t.category_id = c.category_id
    JOIN students s ON t.student_id = s.student_id
    WHERE 1=1 $global_sql
    GROUP BY c.category_name
";
$catResult = mysqli_query($conn, $catDataQuery);
$catLabels = []; $catData = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $catLabels[] = $row['category_name'];
    $catData[] = (int)$row['total'];
}

// 3. Data for Level Chart
$levelDataQuery = "
    SELECT t.level, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN students s ON t.student_id = s.student_id
    WHERE 1=1 $global_sql
    GROUP BY t.level
";
$levelResult = mysqli_query($conn, $levelDataQuery);
$levelLabels = []; $levelData = [];
while ($row = mysqli_fetch_assoc($levelResult)) {
    $levelLabels[] = strtoupper($row['level']);
    $levelData[] = (int)$row['total'];
}

$category_list = mysqli_query($conn, "SELECT * FROM categories");
?>

<style>
    :root { --gov-navy: #002b5e; --gov-gold: #eeb012; }
    body { background-color: #f5f7fa !important; }
    .page-title { color: var(--gov-navy); font-weight: bold; border-left: 5px solid var(--gov-gold); padding-left: 15px; text-transform: uppercase; }
    .card-gov { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); height: 100%; }
    .stat-card { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .btn-gov { background-color: var(--gov-navy); color: #ffffff; font-weight: bold; font-size: 0.8rem; }
    .filter-box { background: #f8f9fa; border-bottom: 1px solid #eee; padding: 12px; margin: -1.5rem -1.5rem 1.5rem -1.5rem; }
</style>

<div class="container mt-4 mb-5">
    <h3 class="page-title mb-4">Advanced Analytics Dashboard</h3>

    <div class="card-gov p-3 mb-4">
        <form method="GET" class="row align-items-end g-2">
            <input type="hidden" name="cat_filter" value="<?= htmlspecialchars($filter_cat) ?>">
            <input type="hidden" name="level_filter" value="<?= htmlspecialchars($filter_level) ?>">
            
            <div class="col-md-9">
                <label class="form-label small fw-bold text-muted mb-1">GLOBAL PROGRAM FILTER</label>
                <select name="program" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    <option value="JTMK" <?= ($filter_program == 'JTMK') ? 'selected' : '' ?>>JTMK - ICT Department</option>
                    <option value="JKA" <?= ($filter_program == 'JKA') ? 'selected' : '' ?>>JKA - Civil Engineering</option>
                    <option value="JKM" <?= ($filter_program == 'JKM') ? 'selected' : '' ?>>JKM - Mechanical Engineering</option>
                    <option value="JKE" <?= ($filter_program == 'JKE') ? 'selected' : '' ?>>JKE - Electrical Engineering</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-gov w-100">APPLY GLOBAL FILTER</button>
            </div>
        </form>
    </div>

    <div class="row mb-4 g-3">
        <div class="col-12">
            <div class="card-gov p-4">
                <div class="filter-box">
                    <form method="GET" class="row g-2">
                        <input type="hidden" name="program" value="<?= htmlspecialchars($filter_program) ?>">
                        <input type="hidden" name="level_filter" value="<?= htmlspecialchars($filter_level) ?>">
                        
                        <div class="col-8">
                            <select name="cat_filter" class="form-select form-select-sm">
                                <option value="">All Talent Categories</option>
                                <?php mysqli_data_seek($category_list, 0); while($c = mysqli_fetch_assoc($category_list)): ?>
                                    <option value="<?= $c['category_id'] ?>" <?= ($filter_cat == $c['category_id']) ? 'selected' : '' ?>><?= $c['category_name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-4 btn-group">
                            <button type="submit" class="btn btn-gov btn-sm">Filter</button>
                            <a href="dashboard.php?program=<?= urlencode($filter_program) ?>&level_filter=<?= urlencode($filter_level) ?>" class="btn btn-light btn-sm border">Reset</a>
                        </div>
                    </form>
                </div>
                <h6 class="fw-bold mb-3 text-uppercase" style="color: var(--gov-navy);">Student Distribution by Category</h6>
                <div style="height: 300px;"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>

        <div class="col-12">
            <div class="card-gov p-4">
                <div class="filter-box">
                    <form method="GET" class="row g-2">
                        <input type="hidden" name="program" value="<?= htmlspecialchars($filter_program) ?>">
                        <input type="hidden" name="cat_filter" value="<?= htmlspecialchars($filter_cat) ?>">
                        
                        <div class="col-8">
                            <select name="level_filter" class="form-select form-select-sm">
                                <option value="">All Achievement Levels</option>
                                <option value="University" <?= ($filter_level == 'University') ? 'selected' : '' ?>>University</option>
                                <option value="State" <?= ($filter_level == 'State') ? 'selected' : '' ?>>State</option>
                                <option value="National" <?= ($filter_level == 'National') ? 'selected' : '' ?>>National</option>
                                <option value="International" <?= ($filter_level == 'International') ? 'selected' : '' ?>>International</option>
                            </select>
                        </div>
                        <div class="col-4 btn-group">
                            <button type="submit" class="btn btn-gov btn-sm">Filter</button>
                            <a href="dashboard.php?program=<?= urlencode($filter_program) ?>&cat_filter=<?= urlencode($filter_cat) ?>" class="btn btn-light btn-sm border">Reset</a>
                        </div>
                    </form>
                </div>
                <h6 class="fw-bold mb-3 text-uppercase" style="color: var(--gov-navy);">Certification Level Analysis</h6>
                <div style="height: 300px;"><canvas id="levelChart"></canvas></div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-md-12">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small fw-bold">TOTAL STUDENTS WITH TALENTS (BASED ON FILTER)</div>
                    <h2 class="fw-bold mb-0" style="color: var(--gov-navy);"><?= $totalStudent ?></h2>
                </div>
                <div class="text-end">
                    <a href="dashboard.php" class="btn btn-outline-danger btn-sm fw-bold">RESET ALL DASHBOARD FILTERS</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#eee' }, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    };

    // Set Warna Berbeza untuk Setiap Bar
    const categoryColors = [
        '#002b5e', '#eeb012', '#1e40af', '#0f766e', 
        '#4338ca', '#b45309', '#374151', '#be185d', 
        '#15803d', '#dc2626'
    ];

    const levelColors = [
        '#eeb012', '#002b5e', '#15803d', '#dc2626'
    ];

    // Category Chart (Bar)
    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($catLabels) ?>,
            datasets: [{
                label: 'Total Talents',
                data: <?= json_encode($catData) ?>,
                backgroundColor: categoryColors, // Menggunakan array warna di sini
                borderRadius: 5
            }]
        },
        options: commonOptions
    });

    // Level Chart (Bar)
    new Chart(document.getElementById('levelChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($levelLabels) ?>,
            datasets: [{
                label: 'Total Talents',
                data: <?= json_encode($levelData) ?>,
                backgroundColor: levelColors, // Menggunakan array warna di sini
                borderRadius: 5
            }]
        },
        options: commonOptions
    });
});
</script>

<?php include "includes/footer.php"; ?>