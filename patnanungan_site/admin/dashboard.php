<?php
require_once '../config.php';
require_once '../functions.php';
require_login();

$user = get_logged_in_user($pdo);
$inquiries = get_inquiries($pdo);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Patnanungan</title>
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
            max-width: 1200px;
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
            font-size: 2rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info p {
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .logout-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #FF7043;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-weight: 700;
        }
        
        .logout-btn:hover {
            background: #E64A19;
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
            font-size: 1.5rem;
            border-bottom: 3px solid #00BFA5;
            padding-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table th {
            background: #006D77;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 700;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:hover {
            background: #f9f9f9;
        }
        
        .status-new {
            background: #FFF3E0;
            color: #E65100;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .status-read {
            background: #E3F2FD;
            color: #0D47A1;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .status-responded {
            background: #E8F5E9;
            color: #1B5E20;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .status-archived {
            background: #F3E5F5;
            color: #4A148C;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 700;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
        
        .stat-box {
            display: inline-block;
            background: #f0f0f0;
            padding: 15px 25px;
            border-radius: 5px;
            margin-right: 15px;
            margin-bottom: 15px;
        }
        
        .stat-box strong {
            color: #006D77;
            font-size: 1.3rem;
        }
        
        .stat-box span {
            display: block;
            color: #666;
            font-size: 0.85rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div>
                <h1>📊 Admin Dashboard</h1>
                <p style="color: #666; margin-top: 5px;">Manage your Patnanungan tourism portal</p>
            </div>
            <div class="user-info">
                <p><strong><?php echo $user['full_name']; ?></strong></p>
                <p><?php echo $user['email']; ?></p>
                <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">Role: <strong><?php echo ucfirst($user['role']); ?></strong></p>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <!-- STATISTICS -->
        <div class="section">
            <h2>Overview</h2>
            <div>
                <div class="stat-box">
                    <strong><?php echo count($inquiries); ?></strong>
                    <span>Total Inquiries</span>
                </div>
                <div class="stat-box">
                    <strong><?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'new')); ?></strong>
                    <span>New Inquiries</span>
                </div>
                <div class="stat-box">
                    <strong><?php echo count(array_filter($inquiries, fn($i) => $i['status'] === 'responded')); ?></strong>
                    <span>Responded</span>
                </div>
            </div>
        </div>

        <!-- INQUIRIES TABLE -->
        <div class="section">
            <h2>📧 Visitor Inquiries</h2>
            
            <?php if (empty($inquiries)): ?>
                <div class="no-data">No inquiries yet. Check back later!</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Visit Date</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><strong>#<?php echo $inquiry['inquiry_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($inquiry['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['email_address']); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['inquiry_type']); ?></td>
                            <td><?php echo $inquiry['planned_visit_date'] ?? 'Not specified'; ?></td>
                            <td>
                                <span class="status-<?php echo $inquiry['status']; ?>">
                                    <?php echo ucfirst($inquiry['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($inquiry['submitted_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- FOOTER -->
        <div style="text-align: center; color: #999; font-size: 0.85rem; margin-top: 30px;">
            <p>Discover Patnanungan — Tourism Portal Management System</p>
        </div>
    </div>
</body>
</html>