<?php
require_once "includes/init.php";
require_login();
require_role(['admin','staff']);

include "includes/header.php";

/* =========================
   STATISTICS QUERIES
========================= */
// Total Students
$totalStudent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM students"))['t'];

// Top Category
$resultTop = mysqli_query($conn, "
    SELECT c.category_name, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN categories c ON t.category_id = c.category_id
    GROUP BY c.category_name
    ORDER BY total DESC LIMIT 1
");
$topCategory = mysqli_fetch_assoc($resultTop);

// Category Stats for Chart
$result = mysqli_query($conn, "
    SELECT c.category_name, COUNT(t.talent_id) AS total
    FROM talents t
    JOIN categories c ON t.category_id = c.category_id
    GROUP BY c.category_name
");

$labels = []; 
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['category_name'];
    $data[] = (int)$row['total'];
}

/* AI INSIGHT LOGIC */
$ai_text = "Insufficient data for analysis.";
if (count($data) > 0) {
    $maxIndex = array_keys($data, max($data))[0];
    $topCat = $labels[$maxIndex];
    $ai_text = "<strong>$topCat</strong> is currently the most active talent category. Consider creating more engagement for other categories to ensure balanced student growth.";
}
?>

<style>
    body { background-color: #f5f7fa !important; }
    .page-title { color: #002b5e; font-weight: bold; border-left: 5px solid #eeb012; padding-left: 15px; text-transform: uppercase; letter-spacing: 1px; }
    .stat-card { background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .stat-title { color: #718096; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; }
    .stat-value { color: #002b5e; font-size: 2rem; font-weight: 800; }
    .card-gov { background: #fff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
</style>

<div class="container mt-4 mb-5">
    <h3 class="page-title mb-4">System Dashboard</h3>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-title">Total Registered Students</div>
                <div class="stat-value"><?= $totalStudent ?></div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="stat-card">
                <div class="stat-title">Top Talent Category</div>
                <div class="stat-value"><?= $topCategory ? htmlspecialchars($topCategory['category_name']) : '-' ?></div>
                <?php if($topCategory): ?>
                    <small class="text-muted"><?= $topCategory['total'] ?> Achievements Recorded</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card-gov p-4 mb-4">
        <h5 class="fw-bold mb-4" style="color: #002b5e;">Student Distribution by Category</h5>
        <div style="height: 400px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <div class="alert shadow-sm border-0 d-flex align-items-center" style="background: #eef4ff; border-left: 5px solid #002b5e !important;">
        <div class="me-3 fs-3 text-primary"><i class="fas fa-robot"></i></div>
        <div>
            <h6 class="fw-bold mb-1" style="color: #002b5e;">AI Data Insight</h6>
            <div class="text-dark small"><?= $ai_text ?></div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Tatasusunan warna berbeza untuk setiap kategori
    const categoryColors = [
        '#002b5e', // Navy Blue
        '#eeb012', // Gold
        '#1e40af', // Royal Blue
        '#0f766e', // Teal
        '#4338ca', // Indigo
        '#b45309', // Amber
        '#1d4ed8', // Blue
        '#374151', // Slate
        '#be185d', // Pink
        '#15803d'  // Green
    ];

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Number of Talents',
                data: <?= json_encode($data) ?>,
                backgroundColor: categoryColors, // Menggunakan array warna
                borderColor: categoryColors,
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#edf2f7' }, 
                    ticks: { color: '#4a5568', stepSize: 1 } 
                },
                x: { 
                    grid: { display: false }, 
                    ticks: { color: '#4a5568', font: { weight: 'bold' } } 
                }
            }
        }
    });
</script>

<?php include "includes/footer.php"; ?>