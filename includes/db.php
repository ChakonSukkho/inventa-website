<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =========================
   DATABASE CONFIG (LOCAL)
========================= */
$host = "inventa-server.mysql.database.azure.com";
$user = "mlvpiarvob@inventa-server";
$pass = "Inventa123";
$db   = "inventa_db";
$port = 3306;

/* =========================
   CONNECT DATABASE
========================= */
$conn = mysqli_connect($host, $user, $pass, $db, $port);

/* =========================
   CHECK CONNECTION
========================= */
if (!$conn) {
    die("DB ERROR: " . mysqli_connect_error());
}
?>