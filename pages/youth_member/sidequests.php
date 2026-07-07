<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/quest_functions.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

$selected_mood = isset($_GET['mood']) ? $_GET['mood'] : null;

// Map moods to quest indices
$mood_quest_map = [
    'panicking' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    'anxious' => [11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
    'depressed' => [21, 22, 23, 24, 25, 26, 27, 28, 29, 30],
    'disassociated' => [31, 32, 33, 34, 35, 36, 37, 38, 39, 40],
    'brainfog' => [41, 42, 43, 44, 45, 46, 47, 48, 49, 50],
    'creative' => [51, 52, 53, 54, 55, 56, 57, 58, 59, 60],
    'happy' => [61, 62, 63, 64, 65, 66, 67, 68, 69, 70]
];

$mood_info = [
    'panicking' => ['name' => 'Panicking / Overwhelmed', 'emoji' => '🚨', 'color' => '#E74C3C'],
    'anxious' => ['name' => 'Anxious / Worried', 'emoji' => '😟', 'color' => '#F39C12'],
    'depressed' => ['name' => 'Depressed / Low Mood', 'emoji' => '😢', 'color' => '#8E44AD'],
    'disassociated' => ['name' => 'Disassociated / Detached', 'emoji' => '🌫️', 'color' => '#95A5A6'],
    'brainfog' => ['name' => 'Brain Fog / No Focus', 'emoji' => '🧠', 'color' => '#3498DB'],
    'creative' => ['name' => 'Creative / Inspired', 'emoji' => '🎨', 'color' => '#E91E63'],
    'happy' => ['name' => 'Happy / Energized', 'emoji' => '😄', 'color' => '#2ECC71']
];

$all_quests = getAllSidequests();
$filtered_quests = [];

if ($selected_mood && isset($mood_quest_map[$selected_mood])) {
    $quest_indices = $mood_quest_map[$selected_mood];
    $filtered_quests = array_slice($all_quests, 0, 10);
} else {
    $filtered_quests = array_slice($all_quests, 0, 10);
}

$quick_start = array_slice($filtered_quests, 0, 3);
$full_activities = array_slice($filtered_quests, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidequests - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%); color: #333; min-height: 100vh; }
        header { background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; padding: 25px 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .header-content h1 { font-size: 32px; font-weight: 700; }
        .nav-links { display: flex; gap: 12px; }
        .nav-links a { color: white; text-decoration: none; font-size: 14px; padding: 10px 18px; border-radius: 6px; transition: all 0.3s; font-weight: 600; }
        .nav-links a:hover { background: rgba(255, 255, 255, 0.2); }
        .logout-btn { background: rgba(255, 255, 255, 0.25); border: 1.5px solid rgba(255, 255, 255, 0.5); }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .page-header { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #4A90E2; text-align: center; }
        .page-header h1 { color: #4A90E2; font-size: 32px; margin-bottom: 10px; }
        .quest-section { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .section-title { color: #6B5344; font-size: 22px; font-weight: 700; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #E8E8E8; }
        .quests-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .quest-card { background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); border: 2px solid #E8E8E8; border-radius: 12px; padding: 22px; transition: all 0.3s; display: flex; flex-direction: column; }
        .quest-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(74, 144, 226, 0.15); border-color: #4A90E2; }
        .quest-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .quest-title { color: #333; font-size: 16px; font-weight: 700; flex: 1; }
        .quest-points { background: #4A90E2; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; white-space: nowrap; margin-left: 10px; }
        .quest-description { color: #666; font-size: 13px; line-height: 1.5; margin-bottom: 15px; flex-grow: 1; }
        .quest-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #EEE; }
        .quest-difficulty { font-size: 12px; font-weight: 600; color: #6B5344; }
        .quest-action { color: #4A90E2; text-decoration: none; font-weight: 700; font-size: 13px; }
        .quest-action:hover { color: #6B5344; }
        .quick-start-badge { display: inline-block; background: #2ECC71; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-right: 8px; }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; text-align: center; } .nav-links { justify-content: center; flex-wrap: wrap; } .quests-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="sidequests.php">Sidequests</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="page-header">
            <h1>
                <?php 
                if ($selected_mood && isset($mood_info[$selected_mood])) {
                    echo $mood_info[$selected_mood]['emoji'] . ' ' . $mood_info[$selected_mood]['name'];
                } else {
                    echo '⚔️ All Sidequests';
                }
                ?>
            </h1>
            <p>Choose your quest and begin your inner journey</p>
        </div>
        
        <?php if ($selected_mood && isset($mood_info[$selected_mood])): ?>
            <!-- QUICK START -->
            <div class="quest-section">
                <div class="section-title">⚡ Quick Start (Fastest Relief)</div>
                <div class="quests-grid">
                    <?php foreach ($quick_start as $quest): ?>
                        <div class="quest-card">
                            <div class="quest-header">
                                <div class="quest-title"><span class="quick-start-badge">FAST</span><?php echo htmlspecialchars($quest['title']); ?></div>
                                <div class="quest-points">+<?php echo $quest['points']; ?></div>
                            </div>
                            <div class="quest-description"><?php echo htmlspecialchars(substr($quest['description'], 0, 60)); ?>...</div>
                            <div class="quest-footer">
                                <div class="quest-difficulty"><?php echo $quest['difficulty']; ?></div>
                                <a href="quest_detail.php?quest_id=<?php echo $quest['quest_id']; ?>&mood=<?php echo $selected_mood; ?>" class="quest-action">Start →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- FULL ACTIVITIES -->
            <div class="quest-section">
                <div class="section-title">📋 Full Activities (Deeper Work)</div>
                <div class="quests-grid">
                    <?php foreach ($full_activities as $quest): ?>
                        <div class="quest-card">
                            <div class="quest-header">
                                <div class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></div>
                                <div class="quest-points">+<?php echo $quest['points']; ?></div>
                            </div>
                            <div class="quest-description"><?php echo htmlspecialchars(substr($quest['description'], 0, 60)); ?>...</div>
                            <div class="quest-footer">
                                <div class="quest-difficulty"><?php echo $quest['difficulty']; ?></div>
                                <a href="quest_detail.php?quest_id=<?php echo $quest['quest_id']; ?>&mood=<?php echo $selected_mood; ?>" class="quest-action">Start →</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>