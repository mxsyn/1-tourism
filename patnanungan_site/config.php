<?php
// ════════════════════════════════════════════════════════════════════
// PATNANUNGAN TOURISM PORTAL - DATABASE CONFIGURATION
// ════════════════════════════════════════════════════════════════════

// Database Credentials
define('DB_HOST', 'localhost');           // Database host
define('DB_USER', 'root');                // Database username
define('DB_PASSWORD', '');                // Database password (empty for localhost)
define('DB_NAME', 'patnanungan_tourism'); // Database name

// Application Settings
define('SITE_URL', 'http://patnanungan_site.local:8080/');
define('ADMIN_URL', SITE_URL . 'admin/');
define('UPLOADS_DIR', $_SERVER['DOCUMENT_ROOT'] . '/patnanungan_site/uploads/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900);     // 15 minutes in seconds

// ────────────────────────────────────────────────────────────────────
// PDO Database Connection
// ────────────────────────────────────────────────────────────────────

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => FALSE,
        ]
    );
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// ────────────────────────────────────────────────────────────────────
// Session Configuration
// ────────────────────────────────────────────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Auto-logout on timeout
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        $_SESSION = [];
    }
    
    $_SESSION['last_activity'] = time();
}

// ────────────────────────────────────────────────────────────────────
// Global Helper Functions
// ────────────────────────────────────────────────────────────────────

/**
 * Sanitize input string
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Redirect to login if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: " . ADMIN_URL . "login.php");
        exit();
    }
}

/**
 * Get current user info from session
 */
function get_logged_in_user($pdo) {
    if (!is_logged_in()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE user_id = ? AND is_active = TRUE");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Log activity to activity_log table
 */
function log_activity($pdo, $action, $table_name = null, $record_id = null, $description = null) {
    if (!is_logged_in()) return;
    
    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $pdo->prepare("
        INSERT INTO activity_log (user_id, action, table_name, record_id, description, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$user_id, $action, $table_name, $record_id, $description, $ip_address]);
}

/**
 * Get flash message from session
 */
function get_flash($key = 'message') {
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

/**
 * Set flash message in session
 */
function set_flash($message, $type = 'info', $key = 'message') {
    $_SESSION[$key] = [
        'text' => $message,
        'type' => $type  // 'success', 'error', 'warning', 'info'
    ];
}

?>
