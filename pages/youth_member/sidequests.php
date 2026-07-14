<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$mood = $_GET['mood'] ?? 'happy';
$mood_map = ['panicking' => 'Panicking/Overwhelmed', 'anxious' => 'Anxious/Worried', 'depressed' => 'Depressed/Low Mood', 'disassociated' => 'Disassociated/Detached', 'brainfog' => 'Brain Fog/No Focus', 'creative' => 'Creative/Inspired', 'happy' => 'Happy/Energized'];

$query = "SELECT * FROM SideQuest WHERE mood = ? AND is_active = 1 ORDER BY points ASC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 's', $mood);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$quests = [];
while ($row = mysqli_fetch_assoc($result)) {
    $quests[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sidequests - InnerCanvas</title>
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
        .header-section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
        .header-section h1 { color: #2c3e50; margin-bottom: 8px; }
        .quests-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; }
        .quest-card { background: white; border: 2px solid #ecf0f1; border-radius: 10px; padding: 18px; transition: all 0.3s; }
        .quest-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); border-color: #16a085; }
        .quest-title { font-size: 16px; font-weight: 700; color: #2c3e50; margin-bottom: 8px; }
        .quest-meta { display: flex; gap: 10px; margin-bottom: 10px; }
        .badge { background: #16a085; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .quest-desc { font-size: 13px; color: #7f8c8d; line-height: 1.5; }
        .quest-link { display: inline-block; margin-top: 12px; padding: 8px 16px; background: #16a085; color: white; text-decoration: none; border-radius: 5px; font-weight: 600; font-size: 12px; }
        .quest-link:hover { background: #138d75; }
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
        <div class="header-section">
            <h1>⚔️ Sidequests for <?php echo htmlspecialchars($mood_map[$mood] ?? 'Unknown'); ?></h1>
            <p style="color: #7f8c8d; margin-top: 8px;">10 unique quests • Pick one and start your journey</p>
        </div>
        
        <div class="quests-grid">
            <?php foreach ($quests as $q): ?>
                <div class="quest-card">
                    <div class="quest-title"><?php echo htmlspecialchars($q['title']); ?></div>
                    <div class="quest-meta">
                        <span class="badge"><?php echo $q['points']; ?> pts</span>
                        <span class="badge"><?php echo htmlspecialchars($q['difficulty']); ?></span>
                    </div>
                    <div class="quest-desc"><?php echo htmlspecialchars($q['description']); ?></div>
                    <a href="quest_detail.php?quest_id=<?php echo $q['quest_id']; ?>" class="quest-link">Start Quest →</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>