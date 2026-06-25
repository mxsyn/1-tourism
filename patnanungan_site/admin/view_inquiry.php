<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

$user = get_logged_in_user($pdo);

// Get inquiry ID from URL
$inquiry_id = $_GET['id'] ?? null;

if (!$inquiry_id) {
    header("Location: dashboard.php");
    exit();
}

// Get inquiry details
$inquiry = get_inquiry($pdo, $inquiry_id);

if (!$inquiry) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response_message = $_POST['response_message'] ?? '';
    
    if (empty($response_message)) {
        $error = "Response message cannot be empty.";
    } else {
        if (respond_to_inquiry($pdo, $inquiry_id, $response_message)) {
            $success = "Response sent successfully!";
            $inquiry = get_inquiry($pdo, $inquiry_id); // Refresh inquiry data
            log_activity($pdo, 'INQUIRY_RESPONDED', 'inquiries', $inquiry_id, 'Admin responded to inquiry');
        } else {
            $error = "Failed to send response. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiry — Patnanungan Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #006D77;
            font-size: 1.8rem;
        }
        
        .back-link {
            color: #00BFA5;
            text-decoration: none;
            font-weight: 600;
            padding: 10px 20px;
            background: #f0f0f0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #00BFA5;
            color: white;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .section h2 {
            color: #006D77;
            margin-bottom: 20px;
            font-size: 1.3rem;
            border-bottom: 3px solid #00BFA5;
            padding-bottom: 10px;
        }
        
        .inquiry-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #00BFA5;
        }
        
        .detail-label {
            color: #006D77;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #333;
            font-size: 1rem;
            word-break: break-word;
        }
        
        .message-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #00BFA5;
            grid-column: 1 / -1;
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 10px;
        }
        
        .status-new {
            background: #FFF3E0;
            color: #E65100;
        }
        
        .status-read {
            background: #E3F2FD;
            color: #0D47A1;
        }
        
        .status-responded {
            background: #E8F5E9;
            color: #1B5E20;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #E8F5E9;
            color: #1B5E20;
            border-left: 4px solid #43A047;
        }
        
        .alert-error {
            background: #FBE9E7;
            color: #BF360C;
            border-left: 4px solid #FF7043;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #006D77;
            font-weight: 700;
            font-size: 0.95rem;
        }
        
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #C8EDEA;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
            min-height: 150px;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #00BFA5;
            box-shadow: 0 0 0 3px rgba(0, 191, 165, 0.1);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #00BFA5;
            color: white;
        }
        
        .btn-primary:hover {
            background: #006D77;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 137, 123, 0.3);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #ddd;
        }
        
        .previous-response {
            background: #E8F5E9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #43A047;
            margin-bottom: 20px;
        }
        
        .previous-response h3 {
            color: #1B5E20;
            margin-bottom: 10px;
        }
        
        .response-text {
            color: #333;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .response-date {
            font-size: 0.85rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>📧 Inquiry Details</h1>
                <p style="color: #666; margin-top: 5px;">Inquiry #<?php echo $inquiry['inquiry_id']; ?></p>
            </div>
            <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        </div>

        <!-- ALERTS -->
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>

        <!-- INQUIRY DETAILS -->
        <div class="section">
            <h2>Visitor Information</h2>
            
            <div>
                <span class="status-badge status-<?php echo $inquiry['status']; ?>">
                    <?php echo strtoupper($inquiry['status']); ?>
                </span>
            </div>
            
            <div class="inquiry-detail">
                <div class="detail-item">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($inquiry['full_name']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Email Address</div>
                    <div class="detail-value"><?php echo htmlspecialchars($inquiry['email_address']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Inquiry Type</div>
                    <div class="detail-value"><?php echo htmlspecialchars($inquiry['inquiry_type']); ?></div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Planned Visit Date</div>
                    <div class="detail-value"><?php echo $inquiry['planned_visit_date'] ?? 'Not specified'; ?></div>
                </div>
                
                <div class="message-section">
                    <div class="detail-label">Message</div>
                    <div class="detail-value" style="white-space: pre-wrap; line-height: 1.6;">
                        <?php echo htmlspecialchars($inquiry['message']); ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Submitted On</div>
                    <div class="detail-value"><?php echo date('M d, Y \a\t h:i A', strtotime($inquiry['submitted_at'])); ?></div>
                </div>
            </div>
        </div>

        <!-- RESPONSE SECTION -->
        <div class="section">
            <h2>📝 Response</h2>
            
            <?php if ($inquiry['status'] === 'responded'): ?>
                <div class="previous-response">
                    <h3>✅ Previously Sent Response:</h3>
                    <div class="response-text"><?php echo htmlspecialchars($inquiry['response_message']); ?></div>
                    <div class="response-date">Sent on: <?php echo date('M d, Y \a\t h:i A', strtotime($inquiry['response_date'])); ?></div>
                </div>
                
                <p style="color: #666; margin-bottom: 20px;">You can update your response below if needed:</p>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="response">Your Response to <?php echo htmlspecialchars($inquiry['full_name']); ?>:</label>
                    <textarea 
                        id="response" 
                        name="response_message" 
                        placeholder="Type your response message here...&#10;&#10;Example:&#10;Dear [Name],&#10;&#10;Thank you for your inquiry about [topic]. We are delighted to help you plan your visit to Patnanungan.&#10;&#10;[Your response details]&#10;&#10;Best regards,&#10;Patnanungan Tourism Office"
                        required></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">✓ Send Response</button>
                    <a href="dashboard.php" class="btn btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center;">Cancel</a>
                </div>
            </form>
        </div>

        <!-- FOOTER -->
        <div style="text-align: center; color: #999; font-size: 0.85rem; margin-top: 30px;">
            <p>Discover Patnanungan — Tourism Portal Management System</p>
        </div>
    </div>
</body>
</html>
