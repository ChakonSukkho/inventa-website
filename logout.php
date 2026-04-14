<?php
require_once "includes/init.php";

session_unset();
session_destroy();

header("Location: login.php");
exit;