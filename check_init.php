<?php
echo "STEP 1<br>";

session_start();
echo "STEP 2 - session ok<br>";

require_once "includes/db.php";
echo "STEP 3 - db ok<br>";

require_once "includes/auth.php";
echo "STEP 4 - auth ok<br>";

require_once "includes/functions.php";
echo "STEP 5 - functions ok<br>";
?>