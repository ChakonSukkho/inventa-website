<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "inventa-server.mysql.database.azure.com";
$user = "mlvpiarvob";
$pass = "Inventa123@";
$db   = "inventa-database";
$port = 3306;

$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

mysqli_real_connect(
    $conn,
    $host,
    $user,
    $pass,
    $db,
    $port,
    NULL,
    MYSQLI_CLIENT_SSL
);

if (mysqli_connect_errno()) {
    die("DB ERROR: " . mysqli_connect_error());
}

?>