<?php
// ════════════════════════════════════════════════════════════════════
// PATNANUNGAN TOURISM PORTAL - ADMIN LOGIN HANDLER
// Secure authentication with bcrypt and session management
// ════════════════════════════════════════════════════════════════════

require_once 'config.php';

// Redirect if already logged in
if (is_logged_in()) {
    header("Location: " . ADMIN_URL . "dashboard.php");
    exit();
}

$error = '';
$success = '';

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        // Rate limiting check
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $lockout_key = 'login_attempts_' . $ip_address;
        
        if (isset($_SESSION[$lockout_key])) {
            $attempts = $_SESSION[$lockout_key];
            
            if ($attempts['count'] >= MAX_LOGIN_ATTEMPTS) {
                // Check if lockout time has passed
                if ((time() - $attempts['timestamp']) < LOCKOUT_TIME) {
                    $remaining = LOCKOUT_TIME - (time() - $attempts['timestamp']);
                    $error = "Too many login attempts. Please try again in " . ceil($remaining / 60) . " minutes.";
                } else {
                    // Reset attempts
                    unset($_SESSION[$lockout_key]);
                }
            }
        }
        
        // Attempt login
        if (empty($error)) {
            try {
                // Fetch admin user
                $stmt = $pdo->prepare("
                    SELECT user_id, username, password_hash, email, full_name, role, is_active 
                    FROM admin_users 
                    WHERE username = ?
                ");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                // Verify user exists and password is correct
                if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
                    // Successful login
                    // Reset login attempts
                    unset($_SESSION[$lockout_key]);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    $_SESSION['last_activity'] = time();
                    
                    // Update last login timestamp
                    $update_stmt = $pdo->prepare("
                        UPDATE admin_users 
                        SET last_login = CURRENT_TIMESTAMP 
                        WHERE user_id = ?
                    ");
                    $update_stmt->execute([$user['user_id']]);
                    
                    // Log activity
                    log_activity($pdo, 'LOGIN', 'admin_users', $user['user_id'], 'Admin user logged in');
                    
                    // Redirect to dashboard
                    header("Location: " . ADMIN_URL . "dashboard.php");
                    exit();
                } else {
                    // Failed login
                    $error = "Invalid username or password.";
                    
                    // Increment login attempts
                    if (!isset($_SESSION[$lockout_key])) {
                        $_SESSION[$lockout_key] = [
                            'count' => 1,
                            'timestamp' => time()
                        ];
                    } else {
                        $_SESSION[$lockout_key]['count']++;
                        $_SESSION[$lockout_key]['timestamp'] = time();
                    }
                    
                    // Log failed attempt
                    log_activity($pdo, 'LOGIN_FAILED', 'admin_users', null, 'Failed login attempt for username: ' . $username);
                }
            } catch (PDOException $e) {
                error_log("Login Error: " . $e->getMessage());
                $error = "An error occurred. Please try again later.";
            }
        }
    }
}

// Display login form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Discover Patnanungan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', system-ui, sans-serif;
            background: linear-gradient(135deg, #00897B 0%, #00BFA5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 137, 123, 0.18);
            padding: 3rem 2rem;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            font-family: 'Playfair Display', Georgia, serif;
            color: #006D77;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #4E6B60;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #006D77;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #C8EDEA;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #00BFA5;
            box-shadow: 0 0 0 3px rgba(0, 191, 165, 0.1);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .alert-error {
            background: #FBE9E7;
            color: #BF360C;
            border-left: 4px solid #FF7043;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #1B5E20;
            border-left: 4px solid #43A047;
        }
        
        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(90deg, #006D77 0%, #00BFA5 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 137, 123, 0.3);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #4E6B60;
        }
        
        .login-footer a {
            color: #00BFA5;
            text-decoration: none;
            font-weight: 700;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Patnanungan</h1>
            <p>Admin Dashboard Login</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Enter your username"
                    required
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password"
                    required
                >
            </div>
            
            <button type="submit" class="login-btn">Sign In</button>
        </form>
        
        <div class="login-footer">
            <p>Forgot your credentials? Contact the site administrator.</p>
        </div>
    </div>
</body>
</html>
