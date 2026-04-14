<?php
require_once "includes/init.php";
require_login();
require_role(['admin','staff']);

include "includes/header.php";

/* =========================
   DASHBOARD FILTERS
========================= */
$filter_program = $_GET['program'] ?? '';
$filter_cat     = $_GET['cat_filter'] ?? '';
$filter_level   = $_GET['level_filter'] ?? '';

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
   STATISTICS
========================= */

// Total Students
$totalStudent = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(DISTINCT s.student_id) AS t 
    FROM students s 
    JOIN talents t ON s.student_id = t.student_id 
    WHERE 1=1 $global_sql
"))['t'];

// Top Category
$topCategoryQuery = "
    SELECT c.category_name, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN categories c ON t.category_id = c.category_id
    JOIN students s ON t.student_id = s.student_id
    WHERE 1=1 $global_sql
    GROUP BY c.category_name
    ORDER BY total DESC
    LIMIT 1
";
$topCategory = mysqli_fetch_assoc(mysqli_query($conn, $topCategoryQuery));

// Category Chart
$catResult = mysqli_query($conn, "
    SELECT c.category_name, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN categories c ON t.category_id = c.category_id
    JOIN students s ON t.student_id = s.student_id
    WHERE 1=1 $global_sql
    GROUP BY c.category_name
");

$catLabels = [];
$catData   = [];
while ($row = mysqli_fetch_assoc($catResult)) {
    $catLabels[] = $row['category_name'];
    $catData[]   = (int)$row['total'];
}

// Level Chart
$levelResult = mysqli_query($conn, "
    SELECT t.level, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN students s ON t.student_id = s.student_id
    WHERE 1=1 $global_sql
    GROUP BY t.level
");

$levelLabels = [];
$levelData   = [];
while ($row = mysqli_fetch_assoc($levelResult)) {
    $levelLabels[] = strtoupper($row['level']);
    $levelData[]   = (int)$row['total'];
}

$category_list = mysqli_query($conn, "SELECT * FROM categories");
?>

<style>
:root { --gov-navy: #002b5e; --gov-gold: #eeb012; }
body { background-color: #f5f7fa !important; }
.page-title { color: var(--gov-navy); font-weight: bold; border-left: 5px solid var(--gov-gold); padding-left: 15px; text-transform: uppercase; }
.card-gov { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
.stat-card { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.btn-gov { background-color: var(--gov-navy); color: #fff; font-weight: bold; }
.filter-box { background: #f8f9fa; padding: 12px; margin-bottom: 15px; border-radius: 6px; }
</style>

<div class="container mt-4 mb-5">
    <h3 class="page-title mb-4">Advanced Analytics Dashboard</h3>

    <!-- GLOBAL FILTER -->
    <div class="card-gov p-3 mb-4">
        <form method="GET" class="row align-items-end g-2">
            <div class="col-md-9">
                <label class="form-label small fw-bold text-muted">GLOBAL PROGRAM FILTER</label>
                <select name="program" class="form-select">
                    <option value="">All Programs</option>
                    <option value="JTMK" <?= ($filter_program=='JTMK')?'selected':'' ?>>JTMK</option>
                    <option value="JKA" <?= ($filter_program=='JKA')?'selected':'' ?>>JKA</option>
                    <option value="JKM" <?= ($filter_program=='JKM')?'selected':'' ?>>JKM</option>
                    <option value="JKE" <?= ($filter_program=='JKE')?'selected':'' ?>>JKE</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-gov w-100">APPLY GLOBAL FILTER</button>
            </div>
        </form>
    </div>

    <!-- STATS -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="text-muted small fw-bold">TOTAL STUDENTS</div>
                <h2><?= $totalStudent ?></h2>
            </div>
        </div>

        <div class="col-md-6">
            <div class="stat-card">
                <div class="text-muted small fw-bold">TOP CATEGORY</div>
                <h3><?= $topCategory['category_name'] ?? 'N/A' ?></h3>
                <small><?= $topCategory['total'] ?? 0 ?> talents</small>
            </div>
        </div>
    </div>

    <!-- CATEGORY CHART -->
    <div class="card-gov p-4 mb-4">
        <div class="filter-box">
            <form method="GET" class="row g-2">
                <div class="col-8">
                    <select name="cat_filter" class="form-select">
                        <option value="">All Categories</option>
                        <?php while($c=mysqli_fetch_assoc($category_list)): ?>
                        <option value="<?= $c['category_id'] ?>"><?= $c['category_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-4 d-flex gap-2">
                    <button class="btn btn-gov w-100">Filter</button>
                </div>
            </form>
        </div>
        <canvas id="categoryChart"></canvas>
    </div>

    <!-- LEVEL CHART -->
    <div class="card-gov p-4">
        <div class="filter-box">
            <form method="GET" class="row g-2">
                <div class="col-8">
                    <select name="level_filter" class="form-select">
                        <option value="">All Levels</option>
                        <option value="University">University</option>
                        <option value="State">State</option>
                        <option value="National">National</option>
                        <option value="International">International</option>
                    </select>
                </div>
                <div class="col-4 d-flex gap-2">
                    <button class="btn btn-gov w-100">Filter</button>
                </div>
            </form>
        </div>
        <canvas id="levelChart"></canvas>
    </div>
</div>

<script>
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($catLabels) ?>,
        datasets: [{
            data: <?= json_encode($catData) ?>,
            backgroundColor: ['#002b5e','#eeb012','#1e40af']
        }]
    }
});

new Chart(document.getElementById('levelChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($levelLabels) ?>,
        datasets: [{
            data: <?= json_encode($levelData) ?>,
            backgroundColor: ['#eeb012','#002b5e','#15803d','#dc2626']
        }]
    }
});
</script>

<?php include "includes/footer.php"; ?>