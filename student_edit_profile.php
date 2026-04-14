<?php
require_once "includes/init.php";
require_login();
require_role(['admin','staff','student']);

if (is_admin() || is_staff()) {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) die("Pelajar tidak sah");
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
} else {
    $user_id = $_SESSION['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) die("Profil tidak dijumpai.");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name    = trim($_POST['student_name']);
    $matric  = trim($_POST['matric_no']);
    $program = trim($_POST['program']);
    $year    = trim($_POST['year_level']);
    $email   = trim($_POST['email']);
    $profile_pic = $student['profile_pic']; // Kekalkan yang lama jika tiada baru

    /* --- LOGIK MUAT NAIK GAMBAR --- */
    if (!empty($_FILES['profile_pic']['name'])) {
        $file = $_FILES['profile_pic'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (in_array($ext, $allowed) && $file['size'] < 2000000) { // Had 2MB
            $new_name = time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], "uploads/" . $new_name)) {
                // Padam gambar lama jika ada
                if (!empty($student['profile_pic']) && file_exists("uploads/" . $student['profile_pic'])) {
                    unlink("uploads/" . $student['profile_pic']);
                }
                $profile_pic = $new_name;
            }
        } else {
            $error = "Fail mestilah gambar (JPG/PNG) dan bawah 2MB.";
        }
    }

    if (empty($error)) {
        $sql = "UPDATE students SET student_name=?, matric_no=?, program=?, year_level=?, email=?, profile_pic=? WHERE student_id=?";
        $stmt_upd = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt_upd, "ssssssi", $name, $matric, $program, $year, $email, $profile_pic, $student['student_id']);
        
        if (mysqli_stmt_execute($stmt_upd)) {
            header("Location: student_profile.php");
            exit;
        }
    }
}

include "includes/header.php";
?>

<style>
    /* Force Light Theme */
    body { background-color: #f8f9fa !important; color: #333 !important; }
    .main-card { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .card-header-gov { background-color: #002b5e; color: #fff; border-bottom: 4px solid #eeb012; padding: 15px; border-radius: 8px 8px 0 0; }
    .btn-gov { background-color: #002b5e; color: #fff; font-weight: bold; border: none; }
    .btn-gov:hover { background-color: #001a38; color: #fff; }
</style>

<div class="container mt-4">
    <div class="main-card">
        <div class="card-header-gov d-flex align-items-center">
            <img src="logo.png" height="30" class="me-3" onerror="this.style.display='none'">
            <h5 class="mb-0">EDIT STUDENT PROFILE</h5>
        </div>
        <div class="p-4">
            <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data"> <div class="text-center mb-4">
                    <label class="form-label d-block fw-bold">Profile Picture</label>
                    <?php if($student['profile_pic']): ?>
                        <img src="uploads/<?= $student['profile_pic'] ?>" class="rounded-circle mb-2" style="width:120px; height:120px; object-fit:cover; border:3px solid #002b5e;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-2" style="width:120px; height:120px; border:3px dashed #ccc;">No Image</div>
                    <?php endif; ?>
                    <input type="file" name="profile_pic" class="form-control mx-auto" style="max-width:300px;">
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="student_name" class="form-control" value="<?= e($student['student_name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Matric Number</label>
                        <input type="text" name="matric_no" class="form-control" value="<?= e($student['matric_no']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Program</label>
                        <input type="text" name="program" class="form-control" value="<?= e($student['program']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Year/Session</label>
                        <input type="number" name="year_level" class="form-control" value="<?= e($student['year_level']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($student['email']) ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-gov w-100 mt-4 py-2">SAVE CHANGES</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>