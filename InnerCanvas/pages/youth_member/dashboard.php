<?php
// ============================================================
// Youth Member Dashboard
// File: pages/youth_member/dashboard.php
// Purpose: Main hub for Youth Members - shows mood, progress, points, streak
// ============================================================

session_start();

require_once '../../includes/auth.php';
require_once '../../includes/mood_functions.php';

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
            background: #f5f5f5;
            color: #333;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-content h1 {
            font-size: 24px;
        }
        
        .header-content p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .card h2 {
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #999;
        }
        
        .mood-card {
            text-align: center;
        }
        
        .mood-emoji {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .mood-check-in {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .mood-check-in h2 {
            color: white;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        
        input[type="range"],
        textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-family: inherit;
        }
        
        input[type="range"] {
            padding: 0;
            height: 8px;
            cursor: pointer;
        }
        
        textarea {
            min-height: 80px;
            resize: vertical;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .mood-value-display {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
            font-weight: bold;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: white;
            color: #667eea;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #f0f0f0;
        }
        
        .message {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .mood-history {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .mood-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
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
            padding: 30px;
            color: #999;
        }
        
        .empty-state p {
            margin: 10px 0;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
            
            .stat-value {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div>
                <h1>InnerCanvas Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
            </div>
            <a href="../../config/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <!-- Motivational Messages -->
        <div style="text-align: center; margin-bottom: 30px; font-style: italic; color: #764ba2;">
            <p><strong>"It is okay not to be okay"</strong> • <strong>"Check in with yourself"</strong></p>
        </div>
        
        <!-- Statistics Grid -->
        <div class="grid">
            <!-- Today's Mood Card -->
            <div class="card mood-card">
                <h2>Today's Mood</h2>
                <?php if ($today_mood): ?>
                    <div class="mood-emoji">
                        <?php 
                            $label = getMoodLabel($today_mood['mood_score']);
                            if ($today_mood['mood_score'] <= 2) echo "😢";
                            elseif ($today_mood['mood_score'] <= 4) echo "😟";
                            elseif ($today_mood['mood_score'] <= 6) echo "😐";
                            elseif ($today_mood['mood_score'] <= 8) echo "😊";
                            else echo "😄";
                        ?>
                    </div>
                    <div class="stat-value"><?php echo $today_mood['mood_score']; ?>/10</div>
                    <div class="stat-label"><?php echo getMoodLabel($today_mood['mood_score']); ?></div>
                <?php else: ?>
                    <div class="empty-state">
                        <p style="font-size: 24px;">📝</p>
                        <p>No mood recorded today</p>
                        <p style="font-size: 12px;">Check in below</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Average Mood Card -->
            <div class="card mood-card">
                <h2>7-Day Average</h2>
                <div class="mood-emoji">
                    <?php 
                        if ($average_mood <= 2) echo "😢";
                        elseif ($average_mood <= 4) echo "😟";
                        elseif ($average_mood <= 6) echo "😐";
                        elseif ($average_mood <= 8) echo "😊";
                        else echo "😄";
                    ?>
                </div>
                <div class="stat-value"><?php echo round($average_mood, 1); ?>/10</div>
                <div class="stat-label">Last 7 days</div>
            </div>
            
            <!-- Mood Streak Card -->
            <div class="card mood-card">
                <h2>Check-In Streak</h2>
                <div class="mood-emoji">🔥</div>
                <div class="stat-value"><?php echo $mood_streak; ?></div>
                <div class="stat-label">Days</div>
            </div>
            
            <!-- Points Card (Placeholder) -->
            <div class="card mood-card">
                <h2>Points</h2>
                <div class="mood-emoji">⭐</div>
                <div class="stat-value">0</div>
                <div class="stat-label">From completed sidequests</div>
            </div>
        </div>
        
        <!-- Mood Check-in Section -->
        <div class="mood-check-in">
            <h2>How are you feeling today?</h2>
            
            <?php if (!empty($mood_message)): ?>
                <div class="message <?php echo $mood_message_type; ?>">
                    <?php echo htmlspecialchars($mood_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="mood_score">Mood Level: <span class="mood-value-display" id="moodDisplay">5</span></label>
                    <input type="range" id="mood_score" name="mood_score" min="1" max="10" value="5" 
                           onchange="document.getElementById('moodDisplay').textContent = this.value">
                    <small style="display: block; margin-top: 10px;">1 = Very Low 😢 ... 10 = Excellent 😄</small>
                </div>
                
                <div class="form-group">
                    <label for="mood_note">Notes (optional)</label>
                    <textarea id="mood_note" name="mood_note" placeholder="What's on your mind? What triggered this mood?"></textarea>
                </div>
                
                <button type="submit" name="record_mood">Record Mood</button>
            </form>
        </div>
        
        <!-- Mood History Section -->
        <div style="margin-top: 30px;">
            <h2 class="section-title">Mood History</h2>
            
            <?php if (count($mood_history) > 0): ?>
                <div class="mood-history">
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
            <?php else: ?>
                <div class="mood-history">
                    <div class="empty-state">
                        <p>No mood records yet. Start tracking your emotions above!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>