<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/quest_functions.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

$quest_id = isset($_GET['quest_id']) ? (int)$_GET['quest_id'] : null;
$mood = isset($_GET['mood']) ? $_GET['mood'] : null;

if (!$quest_id) {
    header("Location: sidequests.php");
    exit();
}

$quest = getSidequestById($quest_id);
if (!$quest) {
    header("Location: sidequests.php");
    exit();
}

$reflection_submitted = false;
$reflection_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reflection'])) {
    $evidence = trim($_POST['evidence'] ?? '');
    
    if (!empty($evidence)) {
        // In real app, save to database
        $reflection_submitted = true;
        $reflection_message = "🎉 Amazing work! Your reflection has been recorded. You earned " . $quest['points'] . " points and completed this quest!";
    }
}

$quest_howtos = [
    1 => 'Notice 5 things you see, 4 you can touch, 3 you hear, 2 you smell, 1 you taste. Ground yourself in sensory reality when panic overwhelms you.',
    2 => 'Splash your face with cold water. Activates your body\'s natural calming response instantly. Come out and breathe slowly.',
    3 => 'Breathe in for 4 counts, hold for 4, out for 4, hold for 4. Repeat 5 times to regulate nervous system.',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quest['title']); ?> - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%); color: #333; }
        header { background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; padding: 25px 20px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
        .header-content { max-width: 900px; margin: 0 auto; }
        .back-btn { color: white; text-decoration: none; font-size: 14px; display: inline-block; font-weight: 600; transition: opacity 0.3s; }
        .back-btn:hover { opacity: 0.8; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .quest-card { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); border-top: 6px solid #4A90E2; }
        h1 { color: #4A90E2; font-size: 32px; margin-bottom: 10px; font-weight: 700; }
        .points-badge { display: inline-block; background: #F39C12; color: white; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; margin-left: 12px; }
        .section { margin: 35px 0; }
        .section-title { color: #6B5344; font-size: 20px; font-weight: 700; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 3px solid #4A90E2; }
        .howto-content { background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); padding: 24px; border-radius: 10px; line-height: 1.8; font-size: 15px; color: #555; border-left: 5px solid #87CEEB; }
        .benefits-list { list-style: none; }
        .benefits-list li { padding: 12px 0; padding-left: 35px; position: relative; font-size: 15px; color: #555; }
        .benefits-list li:before { content: '✓'; position: absolute; left: 0; color: #2ECC71; font-weight: bold; font-size: 20px; }
        .reflection-section { background: linear-gradient(135deg, #E8D5C4 0%, #F5F5F5 100%); padding: 30px; border-radius: 12px; margin-top: 30px; border: 2px solid #87CEEB; }
        .reflection-section h3 { color: #6B5344; margin-bottom: 15px; font-weight: 700; }
        textarea { width: 100%; padding: 14px; border: 2px solid #DDD; border-radius: 8px; font-family: inherit; min-height: 100px; margin-bottom: 15px; }
        textarea:focus { outline: none; border-color: #4A90E2; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        button:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        .success-message { background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%); color: white; padding: 50px 40px; border-radius: 15px; text-align: center; font-size: 18px; line-height: 1.9; border-top: 6px solid #2ECC71; }
        .success-message h2 { font-size: 32px; margin-bottom: 25px; font-weight: 700; }
        .back-link { display: inline-block; margin-top: 25px; padding: 13px 28px; background: white; color: #2ECC71; text-decoration: none; border-radius: 8px; font-weight: 700; transition: all 0.3s; }
        .back-link:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); }
        .difficulty-badge { display: inline-block; background: #6B5344; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; margin-right: 10px; }
        @media (max-width: 768px) { .quest-card { padding: 25px; } h1 { font-size: 24px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="sidequests.php<?php echo $mood ? '?mood=' . $mood : ''; ?>" class="back-btn">← Back to Quests</a>
        </div>
    </header>
    
    <div class="container">
        <?php if ($reflection_submitted): ?>
            <div class="success-message">
                <h2>🎉 Quest Completed!</h2>
                <div style="font-size: 18px; margin: 20px 0; line-height: 1.9;">
                    <?php echo htmlspecialchars($reflection_message); ?>
                    <br><br>
                    <strong>You're building something amazing. Keep going! 💙</strong>
                </div>
                <a href="sidequests.php<?php echo $mood ? '?mood=' . $mood : ''; ?>" class="back-link">Next Quest</a>
            </div>
        <?php else: ?>
            <div class="quest-card">
                <a href="sidequests.php<?php echo $mood ? '?mood=' . $mood : ''; ?>" class="back-btn" style="color: #4A90E2; display: block; margin-bottom: 20px;">← Back</a>
                
                <h1>
                    <?php echo htmlspecialchars($quest['title']); ?>
                    <span class="points-badge">+<?php echo $quest['points']; ?> pts</span>
                </h1>
                <div style="margin-bottom: 20px;">
                    <span class="difficulty-badge"><?php echo $quest['difficulty']; ?></span>
                    <span style="color: #999; font-size: 14px;"><?php echo htmlspecialchars($quest['category']); ?></span>
                </div>
                
                <div class="section">
                    <div class="section-title">📖 How To Do This</div>
                    <div class="howto-content">
                        <?php echo nl2br(htmlspecialchars($quest['description'])); ?>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-title">💡 Why This Matters</div>
                    <ul class="benefits-list">
                        <li>Directly addresses your current emotional state</li>
                        <li>Evidence-based activity for real relief</li>
                        <li>Builds coping skills you can use forever</li>
                        <li>You're taking action for your own healing</li>
                    </ul>
                </div>
                
                <div class="reflection-section">
                    <h3>🎯 Complete This Quest</h3>
                    <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
                        Tell us about your experience. What did you notice? How did it feel? What changed?
                    </p>
                    <form method="POST" action="">
                        <textarea name="evidence" placeholder="I completed this activity and here's what happened..." required></textarea>
                        <button type="submit" name="submit_reflection">Submit & Complete Quest ✓</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>