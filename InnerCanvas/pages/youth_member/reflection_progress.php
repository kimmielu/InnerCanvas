<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/quest_functions.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

// Get completed quests
$completed_quests = getMemberCompletedQuests($member_id);
$total_points = 0;
foreach ($completed_quests as $quest) {
    $total_points += $quest['points'];
}

// Mock reflections (in real app, from database)
$reflections = [
    ['quest' => 'Cold Water Shock Reset', 'mood' => 'Panicking', 'reflection' => 'Wow, that actually worked. My nervous system reset immediately.', 'date' => date('M d, Y', strtotime('-5 days')), 'emoji' => '🚨'],
    ['quest' => 'Box Breathing', 'mood' => 'Anxious', 'reflection' => 'Slowed my heart rate. Helped me think clearly.', 'date' => date('M d, Y', strtotime('-4 days')), 'emoji' => '😟'],
    ['quest' => 'Creative Doodle', 'mood' => 'Creative', 'reflection' => 'Lost myself in the process. Created something beautiful.', 'date' => date('M d, Y', strtotime('-2 days')), 'emoji' => '🎨'],
];
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
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .page-header { background: white; border-radius: 15px; padding: 40px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #4A90E2; text-align: center; }
        .page-header h1 { color: #4A90E2; font-size: 36px; margin-bottom: 15px; font-weight: 700; }
        .page-header p { color: #666; font-size: 16px; margin-bottom: 20px; }
        .stats-banner { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 25px; }
        .stat { background: linear-gradient(135deg, #87CEEB 0%, #E8D5C4 100%); padding: 18px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: 700; color: #4A90E2; }
        .stat-label { font-size: 12px; color: #666; margin-top: 5px; font-weight: 600; }
        .timeline { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .timeline-title { color: #6B5344; font-size: 24px; font-weight: 700; margin-bottom: 30px; text-align: center; }
        .timeline-item { display: flex; gap: 25px; margin-bottom: 40px; position: relative; padding-bottom: 30px; }
        .timeline-item:not(:last-child)::after { content: ''; position: absolute; left: 45px; top: 80px; width: 2px; height: calc(100% + 10px); background: linear-gradient(180deg, #4A90E2 0%, #87CEEB 100%); }
        .timeline-dot { width: 90px; height: 90px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; flex-shrink: 0; box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3); }
        .timeline-content { flex-grow: 1; background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); padding: 22px; border-radius: 12px; border-left: 4px solid #4A90E2; }
        .timeline-date { font-size: 12px; color: #999; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        .timeline-quest { font-size: 18px; font-weight: 700; color: #333; margin-top: 5px; margin-bottom: 8px; }
        .timeline-mood { display: inline-block; background: #E8E8E8; color: #666; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; margin-bottom: 12px; }
        .timeline-reflection { font-size: 15px; color: #555; line-height: 1.6; font-style: italic; border-left: 3px solid #87CEEB; padding-left: 15px; }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state-emoji { font-size: 60px; margin-bottom: 20px; }
        .empty-state-text { color: #999; font-size: 16px; }
        .back-button { display: inline-block; margin-top: 25px; padding: 12px 28px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s; }
        .back-button:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; text-align: center; } .nav-links { justify-content: center; flex-wrap: wrap; } .timeline-item { flex-direction: column; } .timeline-dot { width: 70px; height: 70px; font-size: 32px; } .timeline-item::after { left: 35px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reflection_progress.php">Your Journey</a>
                <a href="sidequests.php">Sidequests</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="page-header">
            <h1>📊 Your Journey</h1>
            <p>Track your growth, reflections, and the quests you've conquered</p>
            
            <div class="stats-banner">
                <div class="stat">
                    <div class="stat-number"><?php echo count($completed_quests); ?></div>
                    <div class="stat-label">Quests Completed</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo $total_points; ?></div>
                    <div class="stat-label">Total Points Earned</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?php echo count(array_unique(array_column($reflections, 'mood'))); ?></div>
                    <div class="stat-label">Moods Addressed</div>
                </div>
            </div>
        </div>
        
        <div class="timeline">
            <div class="timeline-title">🎯 Your Quest Timeline</div>
            
            <?php if (count($reflections) > 0): ?>
                <?php foreach ($reflections as $item): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"><?php echo $item['emoji']; ?></div>
                        <div class="timeline-content">
                            <div class="timeline-date"><?php echo $item['date']; ?></div>
                            <div class="timeline-quest"><?php echo htmlspecialchars($item['quest']); ?></div>
                            <span class="timeline-mood"><?php echo htmlspecialchars($item['mood']); ?></span>
                            <div class="timeline-reflection">"<?php echo htmlspecialchars($item['reflection']); ?>"</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-emoji">🌱</div>
                    <p class="empty-state-text">Your journey is just beginning! Complete quests to see your reflections here.</p>
                    <a href="sidequests.php" class="back-button">Start a Quest</a>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (count($reflections) > 0): ?>
            <div style="text-align: center; margin-top: 40px;">
                <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>