<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "norfaiz.com";
$user = "norfaiz_inventa_user";
$pass = "1nvent@";
$db   = "norfaiz_inventa_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("DB ERROR: " . mysqli_connect_error());
}

echo "DB connected successfully";

?>