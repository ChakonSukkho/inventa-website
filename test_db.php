<?php

$host = "localhost";
$dbname = "norfaiz_inventa_db";
$username = "norfaiz_inventa_user";
$password = "1nvent@";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "✅ Database connected successfully";

?>