<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/mood_functions.php';
require_once '../../includes/quest_functions.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

// Check if user checked in TODAY
$today_mood = getTodayMood($member_id);
$show_reminder = !$today_mood; // Show reminder only if no check-in today

$average_mood = getAverageMood($member_id);
$mood_streak = getMoodStreak($member_id);
$mood_history = getMoodHistory($member_id, 7);
$completed_quests = count(getMemberCompletedQuests($member_id));

$mood_message = "";
$mood_message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_mood'])) {
    $mood_score = (int)$_POST['mood_score'];
    $mood_note = trim($_POST['mood_note'] ?? '');
    
    $result = recordMood($member_id, $mood_score, $mood_note);
    $mood_message = $result['message'];
    $mood_message_type = $result['success'] ? 'success' : 'error';
    
    if ($result['success']) {
        header("refresh:1;url=dashboard.php");
        $show_reminder = false;
    }
}

$achievements = [];
if ($mood_streak >= 7) $achievements[] = ['🔥 Week Warrior', 'Checked in 7 days straight'];
if ($mood_streak >= 30) $achievements[] = ['👑 Month Master', '30-day streak!'];
if ($completed_quests >= 10) $achievements[] = ['🎯 Quest Hero', 'Completed 10 quests'];
if ($completed_quests >= 25) $achievements[] = ['🌟 Wellness Champion', 'Completed 25 quests'];
if ($average_mood >= 7) $achievements[] = ['😊 Mood Booster', 'Weekly mood 7+'];

$moods = [
    ['id' => 'panicking', 'name' => 'Panicking / Overwhelmed', 'emoji' => '🚨', 'color' => '#E74C3C', 'description' => 'Everything feels too much'],
    ['id' => 'anxious', 'name' => 'Anxious / Worried', 'emoji' => '😟', 'color' => '#F39C12', 'description' => 'Worries taking over'],
    ['id' => 'depressed', 'name' => 'Depressed / Low Mood', 'emoji' => '😢', 'color' => '#8E44AD', 'description' => 'Nothing feels worth doing'],
    ['id' => 'disassociated', 'name' => 'Disassociated / Detached', 'emoji' => '🌫️', 'color' => '#95A5A6', 'description' => 'Feel disconnected from body'],
    ['id' => 'brainfog', 'name' => 'Brain Fog / No Focus', 'emoji' => '🧠', 'color' => '#3498DB', 'description' => 'Cannot concentrate'],
    ['id' => 'creative', 'name' => 'Creative / Inspired', 'emoji' => '🎨', 'color' => '#E91E63', 'description' => 'Ideas flowing'],
    ['id' => 'happy', 'name' => 'Happy / Energized', 'emoji' => '😄', 'color' => '#2ECC71', 'description' => 'Feeling good']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InnerCanvas</title>
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
        .reminder-banner { background: linear-gradient(135deg, #FF6B6B 0%, #C44569 100%); color: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; border-left: 6px solid #FF4444; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3); animation: slideDown 0.5s ease-out; }
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .reminder-content h3 { font-size: 18px; font-weight: 700; margin-bottom: 5px; }
        .reminder-content p { font-size: 14px; opacity: 0.95; }
        .close-reminder { background: rgba(255, 255, 255, 0.3); border: none; color: white; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-weight: 700; transition: all 0.3s; }
        .close-reminder:hover { background: rgba(255, 255, 255, 0.5); }
        .welcome-section { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); text-align: center; border-top: 5px solid #4A90E2; }
        .welcome-section h2 { color: #4A90E2; font-size: 26px; margin-bottom: 8px; font-weight: 700; }
        .welcome-section p { color: #666; font-size: 15px; margin-bottom: 5px; }
        .motivational-quotes { color: #6B5344; font-weight: 600; margin-top: 12px; font-size: 13px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08); border-left: 6px solid #4A90E2; transition: all 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.15); }
        .stat-value { font-size: 38px; font-weight: 700; color: #4A90E2; margin-bottom: 8px; }
        .stat-label { font-size: 12px; color: #999; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 600; }
        .achievements-section { background: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #F39C12; }
        .achievements-title { color: #F39C12; font-size: 22px; font-weight: 700; margin-bottom: 20px; }
        .achievements-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; }
        .achievement { background: linear-gradient(135deg, #FFF9E6 0%, #FFFBF0 100%); padding: 18px; border-radius: 10px; border: 2px solid #F39C12; text-align: center; }
        .achievement-emoji { font-size: 36px; margin-bottom: 8px; }
        .achievement-name { font-weight: 700; color: #F39C12; font-size: 14px; margin-bottom: 5px; }
        .achievement-desc { font-size: 12px; color: #666; }
        .mood-selector-section { background: white; border-radius: 15px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .section-title { font-size: 24px; font-weight: 700; color: #4A90E2; margin-bottom: 10px; text-align: center; }
        .section-subtitle { text-align: center; color: #999; margin-bottom: 25px; font-size: 14px; }
        .mood-buttons-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .mood-button { background: white; border: 2px solid #E8E8E8; border-radius: 12px; padding: 22px 18px; text-align: center; cursor: pointer; transition: all 0.3s; text-decoration: none; color: #333; }
        .mood-button:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15); border-color: #4A90E2; }
        .mood-emoji { font-size: 48px; margin-bottom: 10px; display: block; }
        .mood-name { font-weight: 700; font-size: 15px; margin-bottom: 5px; color: #333; }
        .mood-description { font-size: 12px; color: #999; }
        .quick-mood-log { background: linear-gradient(135deg, #87CEEB 0%, #E8D5C4 100%); border-radius: 12px; padding: 25px; margin-top: 25px; }
        .quick-mood-log h3 { color: #6B5344; margin-bottom: 15px; font-size: 16px; font-weight: 700; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; font-size: 14px; }
        input[type="range"], textarea { width: 100%; padding: 10px; border: 2px solid #DDD; border-radius: 8px; font-family: inherit; }
        input[type="range"] { padding: 0; height: 8px; cursor: pointer; }
        textarea { min-height: 65px; resize: vertical; background: rgba(255, 255, 255, 0.85); }
        textarea:focus, input[type="range"]:focus { outline: none; border-color: #4A90E2; }
        .mood-value-display { text-align: center; font-size: 18px; margin: 10px 0; font-weight: bold; color: #6B5344; }
        button { width: 100%; padding: 13px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        button:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.35); }
        .message { padding: 14px; border-radius: 8px; margin-bottom: 18px; font-size: 14px; }
        .message.success { background-color: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }
        .progress-link { display: inline-block; margin-top: 15px; padding: 10px 20px; background: #6B5344; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: all 0.3s; }
        .progress-link:hover { background: #4A90E2; transform: translateY(-2px); }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; text-align: center; } .nav-links { justify-content: center; flex-wrap: wrap; } .mood-buttons-grid { grid-template-columns: repeat(2, 1fr); } .reminder-banner { flex-direction: column; gap: 15px; text-align: center; } }
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
    
    <script src="../../public/js/notification-sound.js"></script>
    <script>
        window.addEventListener('load', function() {
            const reminderBanner = document.getElementById('reminderBanner');
            if (reminderBanner && typeof playNotificationChime === 'function') {
                setTimeout(() => playNotificationChime(), 500);
            }
        });
    </script>
    
    <div class="container">
        <?php if ($show_reminder): ?>
            <div class="reminder-banner" id="reminderBanner">
                <div class="reminder-content">
                    <h3>💙 We missed you!</h3>
                    <p>You haven't checked in today. How are you feeling? Let's start with one check-in.</p>
                </div>
                <button class="close-reminder" onclick="document.getElementById('reminderBanner').style.display='none';">×</button>
            </div>
        <?php endif; ?>
        
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h2>
            <p>How are you feeling today?</p>
            <div class="motivational-quotes">
                <p>"It is okay not to be okay" • "Check in with yourself"</p>
            </div>
            <a href="reflection_progress.php" class="progress-link">📊 View Your Journey</a>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count(getMemberActiveQuests($member_id)); ?></div>
                <div class="stat-label">Active Quests</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo round($average_mood, 1); ?>/10</div>
                <div class="stat-label">Mood Average</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">🔥 <?php echo $mood_streak; ?></div>
                <div class="stat-label">Check-In Streak</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">⚔️ <?php echo $completed_quests; ?></div>
                <div class="stat-label">Quests Completed</div>
            </div>
        </div>
        
        <?php if (count($achievements) > 0): ?>
            <div class="achievements-section">
                <div class="achievements-title">🏆 Your Achievements</div>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement">
                            <div class="achievement-emoji"><?php echo substr($achievement[0], 0, 2); ?></div>
                            <div class="achievement-name"><?php echo substr($achievement[0], 3); ?></div>
                            <div class="achievement-desc"><?php echo $achievement[1]; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mood-selector-section">
            <h2 class="section-title">What are you feeling right now?</h2>
            <p class="section-subtitle">Select your mood to see activities tailored just for how you're feeling</p>
            
            <div class="mood-buttons-grid">
                <?php foreach ($moods as $mood): ?>
                    <a href="sidequests.php?mood=<?php echo htmlspecialchars($mood['id']); ?>" class="mood-button">
                        <span class="mood-emoji"><?php echo $mood['emoji']; ?></span>
                        <div class="mood-name"><?php echo htmlspecialchars($mood['name']); ?></div>
                        <div class="mood-description"><?php echo htmlspecialchars($mood['description']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="quick-mood-log">
                <h3>📝 Or just log your mood:</h3>
                
                <?php if (!empty($mood_message)): ?>
                    <div class="message <?php echo htmlspecialchars($mood_message_type); ?>">
                        <?php echo htmlspecialchars($mood_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>How are you feeling? (1-10)</label>
                        <input type="range" name="mood_score" min="1" max="10" value="5" 
                               onchange="document.getElementById('moodDisplay').textContent = this.value">
                        <div class="mood-value-display" id="moodDisplay">5</div>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea name="mood_note" placeholder="What triggered this mood?"></textarea>
                    </div>
                    
                    <button type="submit" name="record_mood">Record Mood</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>