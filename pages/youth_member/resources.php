<?php
session_start();
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

// Affirmations by mood
$affirmations = [
    'panicking' => [
        'You are safe. This moment will pass.',
        'Your breath is your anchor. Return to it.',
        'Panic is not permanent. You will survive this.',
        'Your nervous system is learning to calm.',
        'This intensity does not define you.'
    ],
    'anxious' => [
        'Worry is trying to protect you. Thank it and let it go.',
        'Unknown futures are also full of possibility.',
        'You are braver than you believe.',
        'This anxiety does not predict the future.',
        'You have survived 100% of hard days so far.'
    ],
    'depressed' => [
        'This heaviness will lift. It always does.',
        'You matter even when you do nothing.',
        'Action before motivation. Small steps count.',
        'Your worth is not determined by productivity.',
        'You are allowed to struggle and still be enough.'
    ],
    'disassociated' => [
        'You are here. You are real. You are safe.',
        'Your body is home. Welcome back.',
        'Presence is a practice. You are learning.',
        'Grounding connects you to life again.',
        'You exist. You matter. You are embodied.'
    ],
    'brainfog' => [
        'Your brain is capable. Give it structure.',
        'Focus is a skill you can strengthen.',
        'One task at a time. That is enough.',
        'Clarity comes with rest and movement.',
        'Your mind deserves gentleness too.'
    ],
    'creative' => [
        'Your creativity is a superpower.',
        'Create without permission. Create without judgment.',
        'Your unique voice matters.',
        'Inspiration flows through you right now.',
        'Make the art only you can make.'
    ],
    'happy' => [
        'You deserve this joy. Savor it.',
        'Happiness is not fragile. It can stay.',
        'Your lightness is beautiful.',
        'Keep creating moments like this.',
        'You are living proof that good things happen.'
    ]
];

// Mental health centers (sample data - in real app, from database)
$centers = [
    ['name' => 'Crisis Text Line', 'type' => 'Emergency Support', 'number' => 'Text HOME to 741741', 'hours' => '24/7'],
    ['name' => 'National Suicide Prevention Lifeline', 'type' => 'Emergency Support', 'number' => '988', 'hours' => '24/7'],
    ['name' => 'SAMHSA National Helpline', 'type' => 'Addiction & Mental Health', 'number' => '1-800-662-4357', 'hours' => '24/7'],
    ['name' => 'BetterHelp Online Therapy', 'type' => 'Counseling', 'number' => 'betterhelp.com', 'hours' => 'Flexible'],
    ['name' => 'Local Counseling Services', 'type' => 'Therapy', 'number' => 'psychology.today', 'hours' => 'Varies'],
];

$selected_mood = isset($_GET['mood']) ? $_GET['mood'] : 'happy';
$daily_affirmation = $affirmations[$selected_mood][array_rand($affirmations[$selected_mood])];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - InnerCanvas</title>
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
        .affirmation-card { background: linear-gradient(135deg, #FFE5B4 0%, #FFDAB9 100%); border-radius: 15px; padding: 40px; margin-bottom: 40px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); border-left: 6px solid #F39C12; text-align: center; }
        .affirmation-card h2 { color: #E67E22; font-size: 28px; margin-bottom: 20px; }
        .affirmation-text { font-size: 18px; color: #8B4513; font-style: italic; line-height: 1.8; font-weight: 500; }
        .centers-section { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #4A90E2; }
        .section-title { color: #4A90E2; font-size: 28px; font-weight: 700; margin-bottom: 30px; }
        .centers-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .center-card { background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); border: 2px solid #E8E8E8; border-radius: 12px; padding: 25px; transition: all 0.3s; }
        .center-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.15); border-color: #4A90E2; }
        .center-name { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .center-type { display: inline-block; background: #4A90E2; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; margin-bottom: 12px; }
        .center-number { font-size: 15px; color: #E67E22; font-weight: 700; margin-bottom: 8px; }
        .center-hours { font-size: 13px; color: #999; }
        .book-btn { display: inline-block; margin-top: 12px; padding: 10px 20px; background: #4A90E2; color: white; text-decoration: none; border-radius: 6px; font-weight: 700; transition: all 0.3s; }
        .book-btn:hover { background: #6B5344; transform: translateY(-2px); }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; text-align: center; } .nav-links { justify-content: center; flex-wrap: wrap; } .affirmation-card { padding: 25px; } .affirmation-text { font-size: 16px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="expression_space.php">Share</a>
                <a href="resources.php">Resources</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="affirmation-card">
            <h2>✨ Today's Affirmation</h2>
            <div class="affirmation-text"><?php echo htmlspecialchars($daily_affirmation); ?></div>
        </div>
        
        <div class="centers-section">
            <h2 class="section-title">💙 Mental Health Resources</h2>
            <p style="color: #666; margin-bottom: 30px; font-size: 16px;">You are not alone. These centers are ready to listen and help.</p>
            
            <div class="centers-grid">
                <?php foreach ($centers as $center): ?>
                    <div class="center-card">
                        <div class="center-name"><?php echo htmlspecialchars($center['name']); ?></div>
                        <span class="center-type"><?php echo htmlspecialchars($center['type']); ?></span>
                        <div class="center-number">📞 <?php echo htmlspecialchars($center['number']); ?></div>
                        <div class="center-hours">⏰ <?php echo htmlspecialchars($center['hours']); ?></div>
                        <a href="#" class="book-btn">Get Help</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>