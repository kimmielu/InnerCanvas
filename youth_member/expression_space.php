<?php
session_start();
require_once dirname(__DIR__) . '/config/db_connection.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$member_id = $user['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['share'])) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_anon = isset($_POST['is_anonymous']) ? 1 : 0;
    
    if (!empty($title) && !empty($content)) {
        $insert = "INSERT INTO ExpressionPost (member_id, title, content, is_anonymous, approval_status, post_date) 
                   VALUES (?, ?, ?, ?, 'pending', NOW())";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, 'issi', $member_id, $title, $content, $is_anon);
        mysqli_stmt_execute($stmt);
    }
}

$posts_query = "SELECT ep.post_id, ep.title, ep.content, ep.post_date, ep.is_anonymous, ym.full_name 
                FROM ExpressionPost ep 
                LEFT JOIN YouthMember ym ON ep.member_id = ym.member_id 
                WHERE ep.approval_status = 'approved' 
                ORDER BY ep.post_date DESC";
$posts_result = mysqli_query($conn, $posts_query);
$posts = [];
while ($row = mysqli_fetch_assoc($posts_result)) {
    $posts[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expression Space - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .form-section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
        .form-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 6px; color: #2c3e50; font-weight: 600; font-size: 14px; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 2px solid #ecf0f1; border-radius: 6px; font-family: 'Segoe UI', sans-serif; }
        textarea { min-height: 100px; }
        input[type="checkbox"] { margin-right: 8px; }
        .checkbox-label { display: flex; align-items: center; color: #555; }
        button { width: 100%; padding: 12px; background: #16a085; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }
        button:hover { background: #138d75; }
        .posts-section { background: white; padding: 25px; border-radius: 12px; }
        .posts-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 20px; }
        .post-card { border-bottom: 2px solid #ecf0f1; padding-bottom: 18px; margin-bottom: 18px; }
        .post-card:last-child { border-bottom: none; }
        .post-title { font-size: 15px; font-weight: 700; color: #2c3e50; }
        .post-meta { color: #95a5a6; font-size: 12px; margin-top: 4px; }
        .post-content { color: #555; margin-top: 10px; line-height: 1.5; font-size: 14px; }
        .empty { text-align: center; color: #95a5a6; padding: 30px; }
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
        <div class="form-section">
            <div class="form-title">✨ Share Your Story</div>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="Give your story a title" required>
                </div>
                <div class="form-group">
                    <label>Your Story</label>
                    <textarea name="content" placeholder="Share your experience, thoughts, or feelings..." required></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_anonymous">
                        Post anonymously
                    </label>
                </div>
                <button type="submit" name="share">Share Your Story</button>
            </form>
        </div>
        
        <div class="posts-section">
            <div class="posts-title">💬 Community Stories</div>
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $p): ?>
                    <div class="post-card">
                        <div class="post-title"><?php echo htmlspecialchars($p['title']); ?></div>
                        <div class="post-meta">By <?php echo $p['is_anonymous'] ? 'Anonymous' : htmlspecialchars($p['full_name']); ?> • <?php echo date('M d, Y', strtotime($p['post_date'])); ?></div>
                        <div class="post-content"><?php echo htmlspecialchars($p['content']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">No approved stories yet. Be the first to share! 🌟</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>