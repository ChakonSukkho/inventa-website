<?php
require_once "includes/init.php";
require_login();
require_role(['student']);

/* =========================
   Validate Talent ID
========================= */
if (!isset($_GET['id'])) {
    header("Location: student_profile.php");
    exit;
}

$talent_id = (int) $_GET['id'];
$user_id   = $_SESSION['user_id'];

/* =========================
   Get Student ID
========================= */
$stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}

$student_id = $student['student_id'];

/* =========================
   Get Talent Record
========================= */
$stmt = mysqli_prepare($conn, "SELECT * FROM talents WHERE talent_id = ? AND student_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $talent_id, $student_id);
mysqli_stmt_execute($stmt);
$talent = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$talent) {
    die("Talent record not found or access denied.");
}

/* =========================
   Handle Update
========================= */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $category_id = (int)$_POST['category_id'];
    $achievement = trim($_POST['achievement']);
    $level       = $_POST['level'];

    $file1 = $talent['certificate'];
    $file2 = $talent['certificate2'];
    $file3 = $talent['certificate3'];
    
    $upload_dir = "uploads/";
    $error = "";

    function updateUpload($file, $old_filename, $upload_dir) {
        if (!empty($file['name'])) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $new_name = time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
                if (!empty($old_filename) && file_exists($upload_dir . $old_filename)) {
                    unlink($upload_dir . $old_filename);
                }
                return $new_name;
            }
        }
        return $old_filename;
    }

    $file1 = updateUpload($_FILES['certificate'], $file1, $upload_dir);
    $file2 = updateUpload($_FILES['certificate2'], $file2, $upload_dir);
    $file3 = updateUpload($_FILES['certificate3'], $file3, $upload_dir);

    // Final Validation: At least one certificate must exist
    if (empty($file1) && empty($file2) && empty($file3)) {
        $error = "At least ONE certificate must remain in the record.";
    } else {
        $stmt_upd = mysqli_prepare($conn,
            "UPDATE talents 
             SET category_id=?, achievement=?, level=?, certificate=?, certificate2=?, certificate3=?
             WHERE talent_id=? AND student_id=?");

        mysqli_stmt_bind_param($stmt_upd, "isssssii",
            $category_id,
            $achievement,
            $level,
            $file1,
            $file2,
            $file3,
            $talent_id,
            $student_id
        );

        if (mysqli_stmt_execute($stmt_upd)) {
            header("Location: student_talents.php");
            exit;
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}

include "includes/header.php";
?>

<style>
    body { background-color: #f5f7fa !important; }
    .page-title { color: #002b5e; font-weight: bold; border-left: 5px solid #eeb012; padding-left: 15px; text-transform: uppercase; letter-spacing: 1px; }
    .form-card { background-color: #ffffff; border-radius: 8px; border: 1px solid #dce1e6; box-shadow: 0 4px 12px rgba(0,0,0,0.06); max-width: 750px; margin: 0 auto; overflow: hidden; }
    .form-header { background-color: #002b5e; color: #ffffff; padding: 20px; text-align: center; font-weight: bold; border-bottom: 4px solid #eeb012; }
    .section-label { font-size: 0.85rem; font-weight: bold; color: #002b5e; text-transform: uppercase; margin-bottom: 15px; display: block; border-bottom: 1px solid #eee; padding-bottom: 5px; }
    .btn-gov { background-color: #002b5e; color: #ffffff; font-weight: bold; padding: 12px; }
    .btn-gov:hover { background-color: #001a38; color: #ffffff; }
    .cert-preview-sm { width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; margin-right: 15px; }
</style>

<div class="container mt-4 mb-5">

    <h3 class="page-title mb-4">Edit Talent Record</h3>

    <div class="form-card">
        <div class="form-header">EDIT ACHIEVEMENT DETAILS</div>
        
        <div class="p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 text-center fw-bold" style="font-size: 0.85rem;"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                
                <span class="section-label">General Information</span>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Category</label>
                    <select class="form-select" name="category_id" required>
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_assoc($cats)){
                            $sel = ($c['category_id'] == $talent['category_id']) ? "selected" : "";
                            echo "<option value='{$c['category_id']}' $sel>".htmlspecialchars($c['category_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">Level</label>
                    <select class="form-select" name="level" required>
                        <?php
                        $levels = ['University','State','National','International'];
                        foreach($levels as $lvl){
                            $sel = ($lvl == $talent['level']) ? "selected" : "";
                            echo "<option value='$lvl' $sel>$lvl</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">Achievement / Award Name</label>
                    <textarea class="form-control" name="achievement" rows="3" required><?= htmlspecialchars($talent['achievement']); ?></textarea>
                </div>

                <span class="section-label mt-4">Certificates & Proof</span>

                <div class="mb-4 d-flex align-items-center p-3 border rounded bg-light">
                    <?php if($talent['certificate']): ?>
                        <img src="uploads/<?= $talent['certificate'] ?>" class="cert-preview-sm">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <label class="form-label fw-bold small text-secondary">Certificate 1 (Main)</label>
                        <input type="file" name="certificate" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center p-3 border rounded bg-light">
                    <?php if($talent['certificate2']): ?>
                        <img src="uploads/<?= $talent['certificate2'] ?>" class="cert-preview-sm">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <label class="form-label fw-bold small text-secondary">Certificate 2</label>
                        <input type="file" name="certificate2" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center p-3 border rounded bg-light">
                    <?php if($talent['certificate3']): ?>
                        <img src="uploads/<?= $talent['certificate3'] ?>" class="cert-preview-sm">
                    <?php endif; ?>
                    <div class="flex-grow-1">
                        <label class="form-label fw-bold small text-secondary">Certificate 3</label>
                        <input type="file" name="certificate3" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>

                <hr>

                <div class="row g-2 mt-2">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-gov w-100">UPDATE RECORD</button>
                    </div>
                    <div class="col-md-4">
                        <a href="student_talents.php" class="btn btn-light border w-100 py-2 fw-bold text-secondary">CANCEL</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>