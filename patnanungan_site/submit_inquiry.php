<?php
// ════════════════════════════════════════════════════════════════════
// PATNANUNGAN TOURISM PORTAL - CONTACT FORM HANDLER
// Processes and stores inquiry submissions
// ════════════════════════════════════════════════════════════════════

require_once 'config.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$full_name = sanitize_input($_POST['full_name'] ?? '');
$email_address = sanitize_input($_POST['email_address'] ?? '');
$inquiry_type = sanitize_input($_POST['inquiry_type'] ?? '');
$planned_visit_date = $_POST['planned_visit_date'] ?? '';
$message = sanitize_input($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required.';
} elseif (strlen($full_name) < 2) {
    $errors[] = 'Full name must be at least 2 characters.';
}

if (empty($email_address)) {
    $errors[] = 'Email address is required.';
} elseif (!validate_email($email_address)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($inquiry_type)) {
    $errors[] = 'Please select an inquiry type.';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
} elseif (strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters long.';
}

// Validate date if provided
if (!empty($planned_visit_date)) {
    $visit_date = DateTime::createFromFormat('Y-m-d', $planned_visit_date);
    if (!$visit_date || $visit_date->format('Y-m-d') !== $planned_visit_date) {
        $errors[] = 'Invalid date format.';
    } elseif ($visit_date < new DateTime()) {
        $errors[] = 'Visit date cannot be in the past.';
    }
}

// Return validation errors
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $errors
    ]);
    exit();
}

// Rate limiting: Check if this email has submitted an inquiry in the last hour
try {
    $rate_limit_stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM inquiries 
        WHERE email_address = ? 
        AND submitted_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");
    $rate_limit_stmt->execute([$email_address]);
    $rate_limit = $rate_limit_stmt->fetch();
    
    if ($rate_limit['count'] >= 3) {
        http_response_code(429);
        echo json_encode([
            'success' => false,
            'message' => 'You have submitted too many inquiries. Please try again later.'
        ]);
        exit();
    }
    
    // Insert inquiry into database
    $insert_stmt = $pdo->prepare("
        INSERT INTO inquiries (full_name, email_address, inquiry_type, planned_visit_date, message, status)
        VALUES (?, ?, ?, ?, ?, 'new')
    ");
    
    $insert_stmt->execute([
        $full_name,
        $email_address,
        $inquiry_type,
        !empty($planned_visit_date) ? $planned_visit_date : NULL,
        $message
    ]);
    
    $inquiry_id = $pdo->lastInsertId();
    
    // Log activity
    log_activity($pdo, 'NEW_INQUIRY_SUBMITTED', 'inquiries', $inquiry_id, 'New inquiry from: ' . $email_address);
    
    // Optional: Send confirmation email to user
    // send_inquiry_confirmation_email($email_address, $full_name);
    
    // Optional: Send notification email to admin
    // send_inquiry_notification_email($email_address, $full_name, $inquiry_type);
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Your inquiry has been submitted successfully. We will get back to you within 1-2 business days.',
        'inquiry_id' => $inquiry_id
    ]);
    
} catch (PDOException $e) {
    error_log("Inquiry Submission Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your inquiry. Please try again later.'
    ]);
}

/**
 * Optional: Send confirmation email to visitor
 */
/*
function send_inquiry_confirmation_email($email, $name) {
    $subject = "Your Inquiry to Patnanungan Tourism";
    $message = "Dear " . $name . ",\n\n";
    $message .= "Thank you for your inquiry about Patnanungan. We have received your message and will respond as soon as possible.\n\n";
    $message .= "Regards,\nPatnanungan Tourism Office";
    
    $headers = "From: tourism@patnanungan.ph\r\n";
    $headers .= "Reply-To: tourism@patnanungan.ph\r\n";
    
    mail($email, $subject, $message, $headers);
}
*/

/**
 * Optional: Send notification email to admin
 */
/*
function send_inquiry_notification_email($email, $name, $type) {
    $admin_email = 'admin@patnanungan.ph';
    $subject = "New Inquiry Submission - " . $type;
    $message = "A new inquiry has been submitted.\n\n";
    $message .= "Name: " . $name . "\n";
    $message .= "Email: " . $email . "\n";
    $message .= "Type: " . $type . "\n\n";
    $message .= "Please log in to the admin panel to view the full message.";
    
    $headers = "From: noreply@patnanungan.ph\r\n";
    
    mail($admin_email, $subject, $message, $headers);
}
*/

?>
