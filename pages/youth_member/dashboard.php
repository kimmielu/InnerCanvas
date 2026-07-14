<?php
session_start();
require_once("../../config/db_connection.php");
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();

if ($user['admin_role'] !== 'none') {
    header("Location: ../../admin/admin_dashboard.php");
    exit();
}

$member_id = $user['member_id'];

$today = date('Y-m-d');
$today_query = "SELECT mood_score FROM MoodEntry WHERE member_id = ? AND DATE(entry_date) = ?";
$stmt = mysqli_prepare($conn, $today_query);
mysqli_stmt_bind_param($stmt, 'is', $member_id, $today);
mysqli_stmt_execute($stmt);
$today_result = mysqli_stmt_get_result($stmt);
$has_mood_today = mysqli_num_rows($today_result) > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['record_mood'])) {
    $mood_score = (int)$_POST['mood_score'];
    $mood_note = trim($_POST['mood_note'] ?? '');
    $insert = "INSERT INTO MoodEntry (member_id, mood_score, mood_note, entry_date) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $insert);
    mysqli_stmt_bind_param($stmt, 'iss', $member_id, $mood_score, $mood_note);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../youth_member/sidequests.php");
        exit();
    }
}

$moods = [
    ['id' => 'panicking', 'name' => 'Panicking/Overwhelmed', 'emoji' => '🚨'],
    ['id' => 'anxious', 'name' => 'Anxious/Worried', 'emoji' => '😟'],
    ['id' => 'depressed', 'name' => 'Depressed/Low Mood', 'emoji' => '😢'],
    ['id' => 'disassociated', 'name' => 'Disassociated/Detached', 'emoji' => '🌫️'],
    ['id' => 'brainfog', 'name' => 'Brain Fog/No Focus', 'emoji' => '🧠'],
    ['id' => 'creative', 'name' => 'Creative/Inspired', 'emoji' => '🎨'],
    ['id' => 'happy', 'name' => 'Happy/Energized', 'emoji' => '😄']
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InnerCanvas</title>
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
        .welcome { background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; text-align: center; }
        .welcome h2 { color: #2c3e50; margin-bottom: 10px; }
        .reminder { background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #c0392b; }
        .mood-section { background: white; padding: 30px; border-radius: 12px; }
        .mood-title { font-size: 24px; font-weight: 700; color: #2c3e50; margin-bottom: 20px; }
        .mood-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .mood-btn { background: white; border: 2px solid #ecf0f1; border-radius: 10px; padding: 18px; text-align: center; cursor: pointer; text-decoration: none; color: #333; transition: all 0.3s; }
        .mood-btn:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); border-color: #16a085; }
        .mood-emoji { font-size: 40px; margin-bottom: 8px; }
        .quick-log { background: #ecf9f7; padding: 20px; border-radius: 8px; margin-top: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; color: #2c3e50; font-weight: 600; }
        input[type="range"], textarea { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px; }
        textarea { min-height: 80px; font-family: 'Segoe UI', sans-serif; }
        button { width: 100%; padding: 12px; background: #16a085; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; margin-top: 10px; }
        button:hover { background: #138d75; }
        .mood-display { text-align: center; color: #16a085; font-weight: 700; font-size: 28px; margin: 10px 0; }
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
        <div class="welcome">
            <h2>Welcome back, <?php echo htmlspecialchars($user['full_name']); ?>! 👋</h2>
            <p>How are you feeling today?</p>
        </div>
        
        <?php if (!$has_mood_today): ?>
            <div class="reminder">💙 You haven't checked in today. Let's see how you're feeling.</div>
        <?php endif; ?>
        
        <div class="mood-section">
            <div class="mood-title">What's your mood?</div>
            <div class="mood-grid">
                <?php foreach ($moods as $mood): ?>
                    <a href="sidequests.php?mood=<?php echo $mood['id']; ?>" class="mood-btn">
                        <div class="mood-emoji"><?php echo $mood['emoji']; ?></div>
                        <div><?php echo $mood['name']; ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="quick-log">
                <h3>Or log your mood:</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Mood Score (1-10)</label>
                        <input type="range" name="mood_score" min="1" max="10" value="5" onchange="document.getElementById('moodVal').textContent = this.value">
                        <div class="mood-display" id="moodVal">5</div>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="mood_note" placeholder="What triggered this mood?"></textarea>
                    </div>
                    <button type="submit" name="record_mood">Record Mood</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>