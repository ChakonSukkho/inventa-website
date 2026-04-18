<?php
require_once "includes/init.php";
require_login();
require_role(['admin']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['csv_file'])) {

    $role = $_POST['role'] ?? 'student';

    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, "r")) !== FALSE) {

        // Skip header row
        fgetcsv($handle);

        $success = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            $name    = trim($data[0] ?? '');
            $ic      = trim($data[1] ?? '');
            $matric  = trim($data[2] ?? '');
            $email   = trim($data[3] ?? '');

            // Skip empty rows
            if (empty($name) || empty($ic)) {
                $skipped++;
                continue;
            }

            // Check duplicate username (IC)
            $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE username = ?");
            mysqli_stmt_bind_param($check, "s", $ic);
            mysqli_stmt_execute($check);
            $result = mysqli_stmt_get_result($check);

            if (mysqli_fetch_assoc($result)) {
                $skipped++;
                continue;
            }

            // STUDENT LOGIC
            if ($role === 'student') {

                if (empty($matric)) {
                    $skipped++;
                    continue;
                }

                // Check duplicate matric
                $check2 = mysqli_prepare($conn, "SELECT student_id FROM students WHERE matric_no = ?");
                mysqli_stmt_bind_param($check2, "s", $matric);
                mysqli_stmt_execute($check2);
                $result2 = mysqli_stmt_get_result($check2);

                if (mysqli_fetch_assoc($result2)) {
                    $skipped++;
                    continue;
                }

                $password = password_hash($matric, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($conn, "
                    INSERT INTO users (username, password, role, must_change_password)
                    VALUES (?, ?, 'student', 1)
                ");
                mysqli_stmt_bind_param($stmt, "ss", $ic, $password);
                mysqli_stmt_execute($stmt);

                $user_id = mysqli_insert_id($conn);

                $stmt2 = mysqli_prepare($conn, "
                    INSERT INTO students (user_id, student_name, matric_no, email)
                    VALUES (?, ?, ?, ?)
                ");
                mysqli_stmt_bind_param($stmt2, "isss", $user_id, $name, $matric, $email);
                mysqli_stmt_execute($stmt2);
            }

            // STAFF LOGIC
            if ($role === 'staff') {

                $department = trim($data[2] ?? ''); // column 3 = department

                $password = password_hash($ic, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($conn, "
                    INSERT INTO users (username, password, role, department, must_change_password)
                    VALUES (?, ?, 'staff', ?, 1)
                ");
                mysqli_stmt_bind_param($stmt, "sss", $ic, $password, $department);
                mysqli_stmt_execute($stmt);
            }

            $success++;
        }

        fclose($handle);

        $message = "✅ Imported: $success $role(s) | ⚠ Skipped: $skipped";
    } else {
        $message = "❌ Failed to read file.";
    }
}
?>

<?php include "includes/header.php"; ?>

<div class="container mt-4 mb-5">

    <h3 class="mb-4">Import Students (CSV)</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
        <form method="POST" enctype="multipart/form-data">

            <!-- ROLE SELECT -->
            <div class="mb-3">
                <label class="form-label fw-bold">Import Type</label>
                <select name="role" class="form-control">
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Upload CSV File</label>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>

            <button type="submit" class="btn btn-primary fw-bold">
                Import Students
            </button>
        </form>
    </div>

    <div class="mt-4">
        <h5>Required CSV Format:</h5>
        <pre>
STUDENT:
student_name,ic_number,matric_no,email

STAFF:
staff_name,ic_number,department,email
        </pre>
    </div>

</div>

<?php include "includes/footer.php"; ?>