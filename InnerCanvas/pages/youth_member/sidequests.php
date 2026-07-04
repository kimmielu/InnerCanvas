<?php
// ============================================================
// Sidequests Page
// File: pages/youth_member/sidequests.php
// Purpose: Browse and accept wellness sidequests
// ============================================================

session_start();

require_once '../../includes/auth.php';
require_once '../../includes/quest_functions.php';

// Require login
requireLogin();

// Get current user
$user = getCurrentUser();
$member_id = $user['member_id'];

// Handle accept quest form
$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_quest'])) {
    $quest_id = (int)$_POST['quest_id'];
    
    $result = acceptSidequest($member_id, $quest_id);
    $message = $result['message'];
    $message_type = $result['success'] ? 'success' : 'error';
}

// Get all available quests
$all_quests = getAllSidequests();

// Get member's active quests
$active_quests = getMemberActiveQuests($member_id);

// Get member's completed quests
$completed_quests = getMemberCompletedQuests($member_id);

// Get accepted quest IDs to hide from browse list
$accepted_quest_ids = array_column($active_quests, 'quest_id');
$completed_quest_ids = array_column($completed_quests, 'quest_id');
$unavailable_quests = array_merge($accepted_quest_ids, $completed_quest_ids);

// Filter available quests
$available_quests = array_filter($all_quests, function($quest) use ($unavailable_quests) {
    return !in_array($quest['quest_id'], $unavailable_quests);
});

// Get member's total points
$total_points = getMemberTotalPoints($member_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidequests - InnerCanvas</title>
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
        
        .nav-links {
            display: flex;
            gap: 20px;
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
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid white;
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        
        .quest-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .quest-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }
        
        .quest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .quest-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .quest-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            flex: 1;
        }
        
        .quest-category {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
            margin-left: 10px;
        }
        
        .quest-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            flex: 1;
        }
        
        .quest-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .points-badge {
            background: #FFD700;
            color: #333;
            padding: 4px 8px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .difficulty-badge {
            padding: 4px 8px;
            border-radius: 5px;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }
        
        .quest-status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .status-accepted {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        button {
            padding: 10px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-transform: uppercase;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
            background: white;
            border-radius: 10px;
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
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .quest-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🎯 Sidequests</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="mood.php">Mood</a>
                <a href="sidequests.php">Sidequests</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo count($active_quests); ?></div>
                <div class="stat-label">Active Quests</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($completed_quests); ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">⭐ <?php echo $total_points; ?></div>
                <div class="stat-label">Total Points</div>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Available Quests Section -->
        <div class="section">
            <div class="section-title">📋 Available Quests</div>
            
            <?php if (count($available_quests) > 0): ?>
                <div class="quest-grid">
                    <?php foreach ($available_quests as $quest): ?>
                        <div class="quest-card">
                            <div class="quest-header">
                                <span class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></span>
                                <span class="quest-category"><?php echo htmlspecialchars($quest['category']); ?></span>
                            </div>
                            
                            <p class="quest-description">
                                <?php echo htmlspecialchars($quest['description']); ?>
                            </p>
                            
                            <div class="quest-meta">
                                <div class="meta-item">
                                    <span class="points-badge">⭐ <?php echo $quest['points']; ?> pts</span>
                                </div>
                                <div class="meta-item">
                                    <span class="difficulty-badge" style="background: <?php echo getDifficultyColor($quest['difficulty']); ?>;">
                                        <?php echo getDifficultyEmoji($quest['difficulty']); ?> <?php echo htmlspecialchars($quest['difficulty']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <form method="POST" action="" style="margin-top: auto;">
                                <input type="hidden" name="quest_id" value="<?php echo $quest['quest_id']; ?>">
                                <button type="submit" name="accept_quest">Accept Quest</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p style="font-size: 24px;">🎉</p>
                    <p><strong>Amazing! You've accepted all available quests!</strong></p>
                    <p style="font-size: 12px;">Check back later for new challenges.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Active Quests Section -->
        <?php if (count($active_quests) > 0): ?>
            <div class="section">
                <div class="section-title">⚡ Your Active Quests</div>
                
                <div class="quest-grid">
                    <?php foreach ($active_quests as $quest): ?>
                        <div class="quest-card">
                            <div class="quest-header">
                                <span class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></span>
                            </div>
                            
                            <p class="quest-description">
                                <?php echo htmlspecialchars($quest['description']); ?>
                            </p>
                            
                            <div class="quest-meta">
                                <div class="meta-item">
                                    <span class="points-badge">⭐ <?php echo $quest['points']; ?> pts</span>
                                </div>
                            </div>
                            
                            <div class="quest-status status-accepted">
                                ✓ Accepted on <?php echo date('M d, Y', strtotime($quest['accepted_date'])); ?>
                            </div>
                            
                            <a href="reflection.php?progress_id=<?php echo $quest['progress_id']; ?>" 
                               style="margin-top: 10px; padding: 10px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; text-align: center; font-weight: 600; display: block;">
                                Submit Evidence & Complete
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Completed Quests Section -->
        <?php if (count($completed_quests) > 0): ?>
            <div class="section">
                <div class="section-title">✅ Completed Quests</div>
                
                <div class="quest-grid">
                    <?php foreach ($completed_quests as $quest): ?>
                        <div class="quest-card">
                            <div class="quest-header">
                                <span class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></span>
                            </div>
                            
                            <div class="quest-meta">
                                <div class="meta-item">
                                    <span class="points-badge">⭐ <?php echo $quest['points_earned']; ?> pts earned</span>
                                </div>
                            </div>
                            
                            <div class="quest-status status-completed">
                                ✓ Completed on <?php echo date('M d, Y', strtotime($quest['completion_date'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>