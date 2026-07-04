<?php
// ============================================================
// Updated Youth Member Dashboard (Fixed Version)
// File: pages/youth_member/dashboard.php
// ============================================================

session_start();

require_once '../../includes/auth.php';
require_once '../../includes/mood_functions.php';
require_once '../../includes/quest_functions.php';

// Require login
requireLogin();

// Get current user
$user = getCurrentUser();
$member_id = $user['member_id'];

// Get today's mood
$today_mood = getTodayMood($member_id);

// Get statistics
$average_mood = getAverageMood($member_id);
$mood_streak = getMoodStreak($member_id);

// Get recent mood history (last 7 days)
$mood_history = getMoodHistory($member_id, 7);

// Handle mood recording (if form submitted)
$mood_message = "";
$mood_message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_mood'])) {
    $mood_score = (int)$_POST['mood_score'];
    $mood_note = trim($_POST['mood_note'] ?? '');
    
    $result = recordMood($member_id, $mood_score, $mood_note);
    $mood_message = $result['message'];
    $mood_message_type = $result['success'] ? 'success' : 'error';
    
    if ($result['success']) {
        // Refresh page to show updated data
        header("refresh:1;url=dashboard.php");
    }
}

// Define the 7 moods
$moods = [
    [
        'id' => 'panicking',
        'name' => 'Panicking / Overwhelmed',
        'emoji' => '🚨',
        'color' => '#E74C3C',
        'description' => 'Everything feels too much'
    ],
    [
        'id' => 'anxious',
        'name' => 'Anxious / Worried',
        'emoji' => '😟',
        'color' => '#F39C12',
        'description' => 'Worries taking over'
    ],
    [
        'id' => 'depressed',
        'name' => 'Depressed / Low Mood',
        'emoji' => '😢',
        'color' => '#8E44AD',
        'description' => 'Nothing feels worth doing'
    ],
    [
        'id' => 'disassociated',
        'name' => 'Disassociated / Detached',
        'emoji' => '🌫️',
        'color' => '#95A5A6',
        'description' => 'Feel disconnected from body'
    ],
    [
        'id' => 'brainfog',
        'name' => 'Brain Fog / No Focus',
        'emoji' => '🧠',
        'color' => '#3498DB',
        'description' => 'Cannot concentrate'
    ],
    [
        'id' => 'creative',
        'name' => 'Creative / Inspired',
        'emoji' => '🎨',
        'color' => '#E91E63',
        'description' => 'Ideas flowing'
    ],
    [
        'id' => 'happy',
        'name' => 'Happy / Energized',
        'emoji' => '😄',
        'color' => '#2ECC71',
        'description' => 'Feeling good'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InnerCanvas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%);
            color: #333;
            min-height: 100vh;
        }
        
        header {
            background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .welcome-section h2 {
            color: #4A90E2;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .welcome-section p {
            color: #666;
            font-size: 14px;
            font-style: italic;
            margin-bottom: 5px;
        }
        
        .motivational-quotes {
            color: #6B5344;
            font-weight: 600;
            margin-top: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #4A90E2;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #4A90E2;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .mood-selector-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #4A90E2;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .mood-buttons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .mood-button {
            background: white;
            border: 2px solid #DDD;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
        }
        
        .mood-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: #4A90E2;
        }
        
        .mood-emoji {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        
        .mood-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .mood-description {
            font-size: 12px;
            color: #999;
        }
        
        .quick-mood-log {
            background: linear-gradient(135deg, #87CEEB 0%, #E8D5C4 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .quick-mood-log h3 {
            color: #6B5344;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="range"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #DDD;
            border-radius: 8px;
            font-family: inherit;
        }
        
        input[type="range"] {
            padding: 0;
            height: 8px;
            cursor: pointer;
        }
        
        textarea {
            min-height: 60px;
            resize: vertical;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .mood-value-display {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
            font-weight: bold;
            color: #6B5344;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.success {
            background-color: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }
        
        .message.error {
            background-color: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }
        
        .mood-history {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-top: 40px;
        }
        
        .mood-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #EEE;
        }
        
        .mood-item:last-child {
            border-bottom: none;
        }
        
        .mood-item-date {
            font-weight: 600;
            color: #333;
        }
        
        .mood-item-score {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .mood-bar {
            width: 50px;
            height: 8px;
            border-radius: 4px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .nav-links {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .mood-buttons-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
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
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
            <p>How are you feeling today?</p>
            <div class="motivational-quotes">
                <p>"It is okay not to be okay" • "Check in with yourself"</p>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count(getMemberActiveQuests($member_id)); ?></div>
                <div class="stat-label">Active Quests</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo round($average_mood, 1); ?>/10</div>
                <div class="stat-label">7-Day Mood Average</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">🔥 <?php echo $mood_streak; ?></div>
                <div class="stat-label">Check-In Streak</div>
            </div>
        </div>
        
        <!-- MAIN: 7 Mood Selector -->
        <div class="mood-selector-section">
            <h2 class="section-title">What are you feeling right now?</h2>
            <p style="text-align: center; color: #999; margin-bottom: 20px; font-size: 14px;">
                Select your mood to see activities tailored just for how you're feeling
            </p>
            
            <div class="mood-buttons-grid">
                <?php foreach ($moods as $mood): ?>
                    <a href="sidequests.php?mood=<?php echo htmlspecialchars($mood['id']); ?>" class="mood-button">
                        <span class="mood-emoji"><?php echo $mood['emoji']; ?></span>
                        <div class="mood-name"><?php echo htmlspecialchars($mood['name']); ?></div>
                        <div class="mood-description"><?php echo htmlspecialchars($mood['description']); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Quick Mood Log -->
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
                        <small style="display: block; margin-top: 10px; color: #666;">
                            1 = Very Low 😢 ... 10 = Excellent 😄
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (optional)</label>
                        <textarea name="mood_note" placeholder="What's on your mind? What triggered this mood?"></textarea>
                    </div>
                    
                    <button type="submit" name="record_mood">Record Mood</button>
                </form>
            </div>
        </div>
        
        <!-- Mood History -->
        <?php if (count($mood_history) > 0): ?>
            <div class="mood-history">
                <h2 class="section-title">Your Mood History (Last 7 Days)</h2>
                
                <?php foreach ($mood_history as $mood): ?>
                    <div class="mood-item">
                        <div>
                            <div class="mood-item-date">
                                <?php echo date('M d, Y', strtotime($mood['entry_date'])); ?>
                            </div>
                            <?php if (!empty($mood['mood_note'])): ?>
                                <div style="font-size: 12px; color: #999; margin-top: 5px;">
                                    <?php echo htmlspecialchars(substr($mood['mood_note'], 0, 50)); ?>...
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mood-item-score">
                            <div class="mood-bar" style="background: <?php echo getMoodColor($mood['mood_score']); ?>; width: <?php echo ($mood['mood_score'] * 5); ?>px;"></div>
                            <span><?php echo $mood['mood_score']; ?>/10</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>