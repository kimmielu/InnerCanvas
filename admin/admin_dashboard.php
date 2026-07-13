<?php
session_start();
require_once dirname(__DIR__) . '/config/db_connection.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();

if (!isset($user['admin_role']) || $user['admin_role'] === 'none') {
    header("Location: ../pages/youth_member/dashboard.php");
    exit();
}

// Handle post actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    
    if ($action === 'approve_post') {
        $update = "UPDATE ExpressionPost SET approval_status = 'approved' WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'reject_post') {
        $update = "UPDATE ExpressionPost SET approval_status = 'rejected' WHERE post_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'approve_resource') {
        $update = "UPDATE MentalHealthResource SET is_approved = 1 WHERE resource_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'reject_resource') {
        $update = "UPDATE MentalHealthResource SET is_approved = 0 WHERE resource_id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
    }
}

// Stats
$today = date('Y-m-d');
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM YouthMember WHERE admin_role = 'none'"))['count'];
$checkin_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM MoodEntry WHERE DATE(entry_date) = '$today'"))['count'];
$quests_completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM QuestProgress WHERE status = 'Completed'"))['count'];
$reflections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM Reflection"))['count'];

// Pending posts
$pending_posts = [];
$posts_result = mysqli_query($conn, "SELECT ep.post_id, ep.title, ep.content, ym.full_name FROM ExpressionPost ep LEFT JOIN YouthMember ym ON ep.member_id = ym.member_id WHERE ep.approval_status = 'pending' ORDER BY ep.post_id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($posts_result)) {
    $pending_posts[] = $row;
}

// Pending resources
$pending_resources = [];
$resources_result = mysqli_query($conn, "SELECT * FROM MentalHealthResource WHERE is_approved = 0 AND is_user_submitted = 1 ORDER BY resource_id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($resources_result)) {
    $pending_resources[] = $row;
}

// Recent members
$recent_members = [];
$members_result = mysqli_query($conn, "SELECT member_id, full_name, email, admin_role FROM YouthMember ORDER BY member_id DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($members_result)) {
    $recent_members[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #16a085; }
        .stat-label { color: #7f8c8d; margin-top: 5px; font-size: 13px; }
        .section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 25px; }
        .section-title { font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; border-bottom: 2px solid #ecf0f1; padding-bottom: 10px; }
        .item { background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px; }
        .item-title { font-weight: 700; color: #2c3e50; }
        .item-content { color: #555; font-size: 13px; margin-top: 5px; line-height: 1.4; }
        .item-meta { color: #95a5a6; font-size: 11px; margin-top: 5px; }
        .action-btns { display: flex; gap: 8px; margin-top: 10px; }
        .btn-approve, .btn-reject { padding: 5px 10px; border: none; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-approve { background: #2ecc71; color: white; }
        .btn-reject { background: #e74c3c; color: white; }
        .btn-approve:hover { background: #27ae60; }
        .btn-reject:hover { background: #c0392b; }
        .empty { text-align: center; color: #95a5a6; padding: 20px; }
        .member-row { background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
        .member-name { font-weight: 700; color: #2c3e50; }
        .member-email { color: #7f8c8d; font-size: 12px; }
        .role-badge { background: #16a085; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: 600; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌿 InnerCanvas Admin</h1>
            <div class="nav-links">
                <a href="admin_dashboard.php">Dashboard</a>
                <?php if ($user['admin_role'] === 'super_admin'): ?>
                    <a href="admin_manage_admins.php">Manage Admins</a>
                <?php endif; ?>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_members; ?></div>
                <div class="stat-label">Youth Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $checkin_today; ?></div>
                <div class="stat-label">Checked In Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $quests_completed; ?></div>
                <div class="stat-label">Quests Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $reflections; ?></div>
                <div class="stat-label">Reflections</div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">📝 Pending Posts for Approval</div>
            <?php if (count($pending_posts) > 0): ?>
                <?php foreach ($pending_posts as $post): ?>
                    <div class="item">
                        <div class="item-title"><?php echo htmlspecialchars($post['title']); ?></div>
                        <div class="item-content"><?php echo htmlspecialchars(substr($post['content'], 0, 100)) . '...'; ?></div>
                        <div class="item-meta">By <?php echo htmlspecialchars($post['full_name'] ?? 'Anonymous'); ?></div>
                        <div class="action-btns">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="approve_post">
                                <input type="hidden" name="id" value="<?php echo $post['post_id']; ?>">
                                <button type="submit" class="btn-approve">Approve</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="reject_post">
                                <input type="hidden" name="id" value="<?php echo $post['post_id']; ?>">
                                <button type="submit" class="btn-reject">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">No pending posts</div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">📚 Pending Resources for Approval</div>
            <?php if (count($pending_resources) > 0): ?>
                <?php foreach ($pending_resources as $res): ?>
                    <div class="item">
                        <div class="item-title"><?php echo htmlspecialchars($res['title']); ?></div>
                        <div class="item-content"><?php echo htmlspecialchars($res['description']); ?></div>
                        <div class="item-meta">By <?php echo htmlspecialchars($res['author']); ?> • <?php echo ucfirst($res['type']); ?></div>
                        <div class="action-btns">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="approve_resource">
                                <input type="hidden" name="id" value="<?php echo $res['resource_id']; ?>">
                                <button type="submit" class="btn-approve">Approve</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="reject_resource">
                                <input type="hidden" name="id" value="<?php echo $res['resource_id']; ?>">
                                <button type="submit" class="btn-reject">Reject</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">No pending resources</div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">👥 Recent Members</div>
            <?php foreach ($recent_members as $member): ?>
                <div class="member-row">
                    <div>
                        <div class="member-name"><?php echo htmlspecialchars($member['full_name']); ?></div>
                        <div class="member-email"><?php echo htmlspecialchars($member['email']); ?></div>
                    </div>
                    <span class="role-badge"><?php echo ucfirst($member['admin_role']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>