<?php
session_start();
require_once dirname(__DIR__, 2) . '/config/db_connection.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$member_id = $user['member_id'];
$quest_id = (int)($_GET['quest_id'] ?? 0);

$query = "SELECT * FROM SideQuest WHERE quest_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $quest_id);
mysqli_stmt_execute($stmt);
$quest = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$quest) {
    header("Location: sidequests.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $reflection = trim($_POST['reflection'] ?? '');
    if (!empty($reflection)) {
        $insert_prog = "INSERT INTO QuestProgress (member_id, quest_id, status, completion_date) VALUES (?, ?, 'Completed', NOW())";
        $stmt_prog = mysqli_prepare($conn, $insert_prog);
        mysqli_stmt_bind_param($stmt_prog, 'ii', $member_id, $quest_id);
        if (mysqli_stmt_execute($stmt_prog)) {
            $prog_id = mysqli_insert_id($conn);
            $insert_ref = "INSERT INTO Reflection (progress_id, reflection_text) VALUES (?, ?)";
            $stmt_ref = mysqli_prepare($conn, $insert_ref);
            mysqli_stmt_bind_param($stmt_ref, 'is', $prog_id, $reflection);
            mysqli_stmt_execute($stmt_ref);
            header("Location: reflection_progress.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quest Detail - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        .quest-header { background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; }
        .quest-title { font-size: 26px; font-weight: 700; color: #2c3e50; margin-bottom: 12px; }
        .quest-info { display: flex; gap: 12px; margin-bottom: 10px; }
        .badge { background: #16a085; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .section { background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; }
        .section-title { font-size: 16px; font-weight: 700; color: #16a085; margin-bottom: 12px; }
        .section-content { color: #555; line-height: 1.6; }
        textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-family: 'Segoe UI', sans-serif; min-height: 100px; margin-bottom: 12px; }
        textarea:focus { outline: none; border-color: #16a085; }
        button { width: 100%; padding: 12px; background: #16a085; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }
        button:hover { background: #138d75; }
        label { display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; }
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
        <div class="quest-header">
            <div class="quest-title"><?php echo htmlspecialchars($quest['title']); ?></div>
            <div class="quest-info">
                <span class="badge"><?php echo $quest['points']; ?> points</span>
                <span class="badge"><?php echo htmlspecialchars($quest['difficulty']); ?></span>
            </div>
        </div>
        
        <div class="section">
            <h3 class="section-title">📋 Description</h3>
            <div class="section-content"><?php echo htmlspecialchars($quest['description']); ?></div>
        </div>
        
        <div class="section">
            <h3 class="section-title">🎯 Benefits</h3>
            <div class="section-content">Completing this quest will help you build resilience and deepen self-awareness. You'll earn <?php echo $quest['points']; ?> points.</div>
        </div>
        
        <div class="section">
            <h3 class="section-title">💭 Your Reflection</h3>
            <form method="POST">
                <label>I completed this activity and here's what happened:</label>
                <textarea name="reflection" placeholder="Share your experience..." required></textarea>
                <button type="submit" name="submit">Complete Quest</button>
            </form>
        </div>
    </div>
</body>
</html>