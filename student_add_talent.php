<?php
require_once "includes/init.php";
require_login();
require_role(['student']);

$user_id = $_SESSION['user_id'];

/* =========================
   GET STUDENT ID
========================= */
$stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student data not found.");
}

$student_id = $student['student_id'];

/* =========================
   HANDLE SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $category_id = (int)$_POST['category_id'];
    $achievement = trim($_POST['achievement']);
    $level       = $_POST['level'];

    $upload_dir = "uploads/";
    $error = "";

    $cert1 = $_FILES['certificate']['name'] ?? '';
    $cert2 = $_FILES['certificate2']['name'] ?? '';
    $cert3 = $_FILES['certificate3']['name'] ?? '';

    // Validation: At least 1 certificate must be uploaded
    if (empty($cert1) && empty($cert2) && empty($cert3)) {
        $error = "Please upload at least ONE achievement certificate.";
    } else {

        function uploadFile($file, $upload_dir) {
            if (!empty($file['name'])) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filename = time() . "_" . uniqid() . "." . $ext;
                if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                    return $filename;
                }
            }
            return null;
        }

        $file1 = uploadFile($_FILES['certificate'], $upload_dir);
        $file2 = uploadFile($_FILES['certificate2'], $upload_dir);
        $file3 = uploadFile($_FILES['certificate3'], $upload_dir);

        $stmt_ins = mysqli_prepare($conn,
            "INSERT INTO talents(student_id, category_id, achievement, level, certificate, certificate2, certificate3)
             VALUES(?,?,?,?,?,?,?)"
        );

        mysqli_stmt_bind_param($stmt_ins, "iisssss",
            $student_id,
            $category_id,
            $achievement,
            $level,
            $file1,
            $file2,
            $file3
        );

        if (mysqli_stmt_execute($stmt_ins)) {
            header("Location: student_talents.php");
            exit;
        } else {
            $error = "Failed to save data: " . mysqli_error($conn);
        }
    }
}

include "includes/header.php";
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

    /* Form Card */
    .form-card {
        background-color: #ffffff;
        border-radius: 8px;
        border: 1px solid #dce1e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        max-width: 700px;
        margin: 0 auto;
        overflow: hidden;
    }

    .form-header {
        background-color: #002b5e;
        color: #ffffff;
        padding: 20px;
        text-align: center;
        font-weight: bold;
        border-bottom: 4px solid #eeb012;
        letter-spacing: 1px;
    }

    .section-label {
        font-size: 0.85rem;
        font-weight: bold;
        color: #002b5e;
        text-transform: uppercase;
        margin-bottom: 15px;
        display: block;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .form-label {
        font-weight: 600;
        color: #555;
        font-size: 0.9rem;
    }

    /* Buttons */
    .btn-gov {
        background-color: #002b5e;
        color: #ffffff;
        font-weight: bold;
        padding: 12px;
        transition: 0.3s;
    }

    .btn-gov:hover {
        background-color: #001a38;
        color: #ffffff;
    }

    /* Input Styling */
    .form-control, .form-select {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
    }

    .form-control:focus {
        border-color: #002b5e;
        box-shadow: 0 0 0 3px rgba(0, 43, 94, 0.1);
    }
</style>

<div class="container mt-4 mb-5">

    <h3 class="page-title mb-4">Add Student Talent</h3>

    <div class="form-card">
        <div class="form-header">
            TALENT & ACHIEVEMENT REGISTRATION FORM
        </div>
        
        <div class="p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 text-center fw-bold" style="font-size: 0.85rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                
                <span class="section-label">Achievement Details</span>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">-- Please Select Category --</option>
                        <?php
                        $cats = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_assoc($cats)){
                            echo "<option value='{$c['category_id']}'>".htmlspecialchars($c['category_name'])."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Level of Achievement</label>
                    <select class="form-select" name="level" required>
                        <option value="">-- Please Select Level --</option>
                        <option value="University">University</option>
                        <option value="State">State</option>
                        <option value="National">National</option>
                        <option value="International">International</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Achievement / Award Name</label>
                    <textarea class="form-control" name="achievement" rows="3" placeholder="Example: Champion of National Debate Competition" required></textarea>
                </div>

                <span class="section-label mt-4">Proof of Achievement (Certificates/Documents)</span>
                <p class="text-muted small mb-3">Please upload at least one certificate in Image (JPG/PNG) or PDF format.</p>

                <div class="mb-3">
                    <label class="form-label">Certificate 1 (Main)</label>
                    <input type="file" name="certificate" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="mb-3">
                    <label class="form-label">Certificate 2 (Additional)</label>
                    <input type="file" name="certificate2" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <div class="mb-4">
                    <label class="form-label">Certificate 3 (Additional)</label>
                    <input type="file" name="certificate3" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <hr>

                <div class="row g-2 mt-2">
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-gov w-100">SAVE TALENT RECORD</button>
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