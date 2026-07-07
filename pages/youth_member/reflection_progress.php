<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../config/db_connection.php';
require_once '../../includes/quest_functions.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

// Get all completed quests WITH their reflections (real data)
$query = "
    SELECT 
        qp.progress_id,
        sq.title,
        sq.points,
        sq.category,
        qp.completion_date,
        r.reflection_text,
        qp.status
    FROM QuestProgress qp
    JOIN SideQuest sq ON qp.quest_id = sq.quest_id
    LEFT JOIN Reflection r ON qp.progress_id = r.progress_id
    WHERE qp.member_id = ? AND qp.status = 'Completed'
    ORDER BY qp.completion_date DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $member_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$completed_quests = [];
$total_points = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $completed_quests[] = $row;
        $total_points += $row['points'];
    }
}

// Calculate stats
$total_completed = count($completed_quests);
$unique_categories = count(array_unique(array_column($completed_quests, 'category')));
$avg_points_per_quest = $total_completed > 0 ? round($total_points / $total_completed, 1) : 0;

// Get mood streak (consecutive days with check-in)
$streak_query = "
    SELECT COUNT(DISTINCT DATE(entry_date)) as days_with_checkIn
    FROM MoodEntry
    WHERE member_id = ?
    AND entry_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";
$streak_stmt = mysqli_prepare($conn, $streak_query);
mysqli_stmt_bind_param($streak_stmt, 'i', $member_id);
mysqli_stmt_execute($streak_stmt);
$streak_result = mysqli_stmt_get_result($streak_stmt);
$streak_row = mysqli_fetch_assoc($streak_result);
$monthly_checkins = $streak_row['days_with_checkIn'] ?? 0;

// Optional: Filter by category/mood
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$filtered_quests = $completed_quests;

if ($selected_category !== 'all') {
    $filtered_quests = array_filter($completed_quests, function($q) use ($selected_category) {
        return $q['category'] === $selected_category;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Journey - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%); color: #333; }
        header { background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; padding: 25px 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .header-content h1 { font-size: 32px; font-weight: 700; }
        .nav-links { display: flex; gap: 12px; }
        .nav-links a { color: white; text-decoration: none; font-size: 14px; padding: 10px 18px; border-radius: 6px; transition: all 0.3s; font-weight: 600; }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.2); }
        .logout-btn { background: rgba(255, 255, 255, 0.25); border: 1.5px solid rgba(255, 255, 255, 0.5); }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .page-header { background: white; border-radius: 15px; padding: 35px; margin-bottom: 35px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #4A90E2; text-align: center; }
        .page-header h1 { color: #4A90E2; font-size: 32px; margin-bottom: 10px; font-weight: 700; }
        .page-header p { color: #666; font-size: 15px; margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; }
        .stat { background: linear-gradient(135deg, #87CEEB 0%, #E8D5C4 100%); padding: 18px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 28px; font-weight: 700; color: #4A90E2; }
        .stat-label { font-size: 11px; color: #666; margin-top: 5px; font-weight: 700; text-transform: uppercase; }
        .filter-section { background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06); }
        .filter-label { font-weight: 700; margin-bottom: 12px; color: #333; font-size: 14px; }
        .filter-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; border: 2px solid #E8E8E8; background: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s; }
        .filter-btn:hover { border-color: #4A90E2; }
        .filter-btn.active { background: #4A90E2; color: white; border-color: #4A90E2; }
        .completions { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .completions-title { color: #6B5344; font-size: 22px; font-weight: 700; margin-bottom: 25px; }
        .completion-item { background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); border: 2px solid #E8E8E8; border-radius: 12px; padding: 22px; margin-bottom: 18px; transition: all 0.3s; }
        .completion-item:hover { transform: translateY(-2px); border-color: #4A90E2; box-shadow: 0 6px 18px rgba(74, 144, 226, 0.1); }
        .quest-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .quest-title { font-size: 17px; font-weight: 700; color: #333; }
        .quest-meta { display: flex; gap: 12px; font-size: 12px; }
        .meta-badge { background: #4A90E2; color: white; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
        .meta-date { color: #999; }
        .quest-points { font-size: 16px; font-weight: 700; color: #F39C12; }
        .reflection-box { background: linear-gradient(135deg, #FFF9E6 0%, #FFFBF0 100%); border-left: 4px solid #F39C12; padding: 15px; border-radius: 8px; margin-top: 12px; }
        .reflection-label { font-size: 12px; font-weight: 700; color: #E67E22; text-transform: uppercase; margin-bottom: 8px; }
        .reflection-text { font-size: 14px; color: #555; line-height: 1.6; font-style: italic; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-emoji { font-size: 56px; margin-bottom: 18px; }
        .empty-text { color: #999; font-size: 16px; margin-bottom: 20px; }
        .start-btn { display: inline-block; padding: 12px 28px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s; }
        .start-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        .back-link { display: inline-block; margin-top: 25px; color: #4A90E2; text-decoration: none; font-weight: 700; transition: color 0.3s; }
        .back-link:hover { color: #6B5344; }
        @media (max-width: 768px) { 
            .header-content { flex-direction: column; gap: 15px; text-align: center; } 
            .nav-links { justify-content: center; flex-wrap: wrap; } 
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .quest-header { flex-direction: column; }
            .filter-buttons { flex-direction: column; }
            .filter-btn { width: 100%; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reflection_progress.php">Your Journey</a>
                <a href="expression_space.php">Share</a>
                <a href="resources.php">Resources</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="page-header">
            <h1>📊 Your Wellness Journey</h1>
            <p>Track the quests you've completed, the growth you've achieved</p>
            
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-number"><?php echo $total_completed; ?></div>
                    <div class="stat-label">Quests Done</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $total_points; ?></div>
                    <div class="stat-label">Points Earned</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $unique_categories; ?></div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $monthly_checkins; ?></div>
                    <div class="stat-label">Check-ins (30d)</div>
                </div>
            </div>
        </div>
        
        <?php if ($total_completed > 0): ?>
            <!-- Filter by category -->
            <div class="filter-section">
                <div class="filter-label">Filter by Activity Type:</div>
                <div class="filter-buttons">
                    <a href="?category=all" class="filter-btn <?php echo $selected_category === 'all' ? 'active' : ''; ?>">All Activities</a>
                    <?php 
                    $categories = array_unique(array_column($completed_quests, 'category'));
                    foreach ($categories as $cat): 
                    ?>
                        <a href="?category=<?php echo urlencode($cat); ?>" class="filter-btn <?php echo $selected_category === $cat ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Completed quests with reflections -->
            <div class="completions">
                <h2 class="completions-title">✅ Your Completed Quests</h2>
                
                <?php foreach ($filtered_quests as $quest): ?>
                    <div class="completion-item">
                        <div class="quest-header">
                            <div class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></div>
                            <div class="quest-points">+<?php echo $quest['points']; ?> pts</div>
                        </div>
                        
                        <div class="quest-meta">
                            <span class="meta-badge"><?php echo htmlspecialchars($quest['category']); ?></span>
                            <span class="meta-date">📅 <?php echo date('M d, Y', strtotime($quest['completion_date'])); ?></span>
                        </div>
                        
                        <?php if (!empty($quest['reflection_text'])): ?>
                            <div class="reflection-box">
                                <div class="reflection-label">💭 Your Reflection:</div>
                                <div class="reflection-text">
                                    "<?php echo nl2br(htmlspecialchars($quest['reflection_text'])); ?>"
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty state -->
            <div class="completions">
                <div class="empty-state">
                    <div class="empty-emoji">🌱</div>
                    <p class="empty-text">Your journey is just beginning. Complete quests to see your growth here.</p>
                    <a href="sidequests.php" class="start-btn">Start a Quest</a>
                </div>
            </div>
        <?php endif; ?>
        
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>