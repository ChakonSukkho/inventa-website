<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =========================
   DATABASE CONFIG (LOCAL)
========================= */
$host = "localhost";
$user = "root";
$pass = "";
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