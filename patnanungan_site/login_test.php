<?php
require_once 'config.php';

$username = 'admin';
$password = 'admin123';

echo "<h2>Login Test</h2>";

try {
    // Query the database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✓ User found: " . $user['username'] . "<br>";
        echo "✓ Password hash: " . substr($user['password_hash'], 0, 20) . "...<br>";
        
        // Test password
        if (password_verify($password, $user['password_hash'])) {
            echo "✓✓✓ PASSWORD MATCHES! Login should work!<br>";
        } else {
            echo "✗ Password does NOT match<br>";
        }
    } else {
        echo "✗ User NOT found in database<br>";
    }
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage();
}
?>