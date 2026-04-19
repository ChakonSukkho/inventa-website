<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "inventa.my";
$user = "norfaiz_inventa_user";
$pass = "1nvent@";
$db   = "norfaiz_inventa_db";
$port = 3306;

// detect Azure MySQL (biasanya ada .mysql.database.azure.com)
if (strpos($host, "azure") !== false) {

    // 🔵 Azure connection (guna SSL)
    $conn = mysqli_init();
    mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

    mysqli_real_connect(
        $conn,
        $host,
        $user,
        $pass,
        $db,
        3306,
        NULL,
        MYSQLI_CLIENT_SSL
    );

} else {

    // 🟢 cPanel / normal MySQL
    $conn = mysqli_connect($host, $user, $pass, $db);
}

// check connection
if (!$conn || mysqli_connect_errno()) {
    die("DB ERROR: " . mysqli_connect_error());
}

// echo "DB connected successfully"; // guna untuk test je
?>