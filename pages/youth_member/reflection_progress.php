<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$member_id = $user['member_id'];

$query = "SELECT qp.progress_id, qp.quest_id, qp.completion_date, sq.title, sq.points FROM QuestProgress qp 
          JOIN SideQuest sq ON qp.quest_id = sq.quest_id 
          WHERE qp.member_id = ? AND qp.status = 'Completed' 
          ORDER BY qp.completion_date DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $member_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$quests_completed = [];
while ($row = mysqli_fetch_assoc($result)) {
    $quests_completed[] = $row;
}

$total_points = 0;
foreach ($quests_completed as $q) {
    $total_points += $q['points'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Journey - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: 700; color: #16a085; }
        .stat-label { color: #7f8c8d; margin-top: 5px; font-size: 14px; }
        .timeline { background: white; border-radius: 12px; padding: 25px; }
        .timeline-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 20px; }
        .timeline-item { border-left: 3px solid #16a085; padding-left: 20px; margin-bottom: 25px; position: relative; }
        .timeline-item:before { content: ''; position: absolute; left: -9px; top: 0; width: 12px; height: 12px; background: #16a085; border-radius: 50%; }
        .quest-name { font-weight: 700; color: #2c3e50; font-size: 15px; }
        .quest-date { color: #95a5a6; font-size: 12px; margin-bottom: 8px; }
        .reflection { background: #f8f9fa; padding: 12px; border-radius: 6px; color: #555; font-size: 13px; line-height: 1.5; font-family: 'Segoe UI', sans-serif; }
        .points { display: inline-block; background: #F39C12; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 10px; }
        .empty { text-align: center; color: #95a5a6; padding: 40px; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌿 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reflection_progress.php">Your Journey</a>
                <a href="sidequests.php">Sidequests</a>
                <a href="expression_space.php">Share</a>
                <a href="resources.php">Resources</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($quests_completed); ?></div>
                <div class="stat-label">Quests Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_points; ?></div>
                <div class="stat-label">Total Points Earned</div>
            </div>
        </div>
        
        <div class="timeline">
            <div class="timeline-title">🏆 Your Journey Timeline</div>
            <?php if (count($quests_completed) > 0): ?>
                <?php foreach ($quests_completed as $q): ?>
                    <div class="timeline-item">
                        <div class="quest-date"><?php echo date('M d, Y', strtotime($q['completion_date'])); ?></div>
                        <div class="quest-name"><?php echo htmlspecialchars($q['title']); ?> <span class="points"><?php echo $q['points']; ?> pts</span></div>
                        <?php
                            $ref_query = "SELECT reflection_text FROM Reflection WHERE progress_id = ?";
                            $ref_stmt = mysqli_prepare($conn, $ref_query);
                            mysqli_stmt_bind_param($ref_stmt, 'i', $q['progress_id']);
                            mysqli_stmt_execute($ref_stmt);
                            $ref = mysqli_fetch_assoc(mysqli_stmt_get_result($ref_stmt));
                            if ($ref):
                        ?>
                            <div class="reflection">"<?php echo htmlspecialchars($ref['reflection_text']); ?>"</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">No quests completed yet. Start one today! 🚀</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>