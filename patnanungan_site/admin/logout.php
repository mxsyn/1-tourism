<?php
require_once '../config.php';

// Clear session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to login
header("Location: " . ADMIN_URL . "login.php");
exit();
?>