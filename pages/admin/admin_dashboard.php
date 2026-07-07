<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../config/db_connection.php';

requireLogin();
$user = getCurrentUser();

// Check if admin (simplified - in real app, check role in database)
if ($user['username'] !== 'admin') {
    header("Location: ../youth_member/dashboard.php");
    exit();
}

// Mock data
$total_members = 247;
$active_today = 89;
$quests_completed = 1543;
$resources_available = 12;

$members = [
    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'joined' => 'Jan 15, 2024', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'joined' => 'Feb 03, 2024', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Alex Johnson', 'email' => 'alex@example.com', 'joined' => 'Jan 28, 2024', 'status' => 'Inactive'],
];

$pending_posts = [
    ['id' => 1, 'author' => 'Anonymous', 'title' => 'My Anxiety Journey', 'status' => 'Pending', 'date' => 'Today'],
    ['id' => 2, 'author' => 'Jordan', 'title' => 'How I Beat Depression', 'status' => 'Pending', 'date' => 'Yesterday'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%); color: #333; }
        header { background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; padding: 25px 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .header-content h1 { font-size: 32px; font-weight: 700; }
        .admin-badge { background: rgba(255, 255, 255, 0.3); padding: 8px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .logout-btn { color: white; text-decoration: none; padding: 8px 16px; border-radius: 6px; background: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.5); transition: all 0.3s; font-weight: 600; }
        .logout-btn:hover { background: rgba(255, 255, 255, 0.4); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08); border-left: 6px solid #4A90E2; }
        .stat-value { font-size: 36px; font-weight: 700; color: #4A90E2; margin-bottom: 8px; }
        .stat-label { font-size: 12px; color: #999; text-transform: uppercase; font-weight: 600; }
        .section { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .section-title { color: #6B5344; font-size: 22px; font-weight: 700; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #F5F5F5; padding: 12px; text-align: left; font-weight: 700; font-size: 13px; color: #333; border-bottom: 2px solid #E8E8E8; }
        table td { padding: 14px 12px; border-bottom: 1px solid #E8E8E8; }
        table tr:hover { background: #F9F9F9; }
        .status-badge { display: inline-block; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .status-active { background: #D4EDDA; color: #155724; }
        .status-inactive { background: #F8D7DA; color: #721C24; }
        .status-pending { background: #FFF3CD; color: #856404; }
        .action-btn { padding: 8px 14px; background: #4A90E2; color: white; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 700; transition: all 0.3s; cursor: pointer; border: none; }
        .action-btn:hover { background: #6B5344; transform: translateY(-2px); }
        .approve-btn { background: #2ECC71; }
        .approve-btn:hover { background: #27AE60; }
        .reject-btn { background: #E74C3C; }
        .reject-btn:hover { background: #C0392B; }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; } .stats-grid { grid-template-columns: 1fr; } table { font-size: 13px; } table th, table td { padding: 8px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas Admin</h1>
            <div style="display: flex; gap: 15px; align-items: center;">
                <span class="admin-badge">👑 Administrator</span>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $total_members; ?></div>
                <div class="stat-label">Total Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $active_today; ?></div>
                <div class="stat-label">Active Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $quests_completed; ?></div>
                <div class="stat-label">Quests Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $resources_available; ?></div>
                <div class="stat-label">Resources Available</div>
            </div>
        </div>
        
        <!-- Members Section -->
        <div class="section">
            <h2 class="section-title">👥 Recent Members</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($member['name']); ?></td>
                            <td><?php echo htmlspecialchars($member['email']); ?></td>
                            <td><?php echo htmlspecialchars($member['joined']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $member['status'] === 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo htmlspecialchars($member['status']); ?>
                                </span>
                            </td>
                            <td><a href="#" class="action-btn">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Posts Moderation Section -->
        <div class="section">
            <h2 class="section-title">📝 Pending Posts (Moderation)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['date']); ?></td>
                            <td>
                                <span class="status-badge status-pending">
                                    <?php echo htmlspecialchars($post['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="#" class="action-btn approve-btn">Approve</a>
                                <a href="#" class="action-btn reject-btn" style="margin-left: 5px;">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>