<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "Password: " . $password . "<br>";
echo "Hash: " . $hash . "<br>";
echo "<br>";
echo "Copy this hash and paste it in the database!";
?>