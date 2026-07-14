<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$member_id = $user['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_resource'])) {
    $title = trim($_POST['resource_title'] ?? '');
    $author = trim($_POST['resource_author'] ?? '');
    $type = $_POST['resource_type'] ?? 'article';
    $link = trim($_POST['resource_link'] ?? '');
    $desc = trim($_POST['resource_desc'] ?? '');
    
    if (!empty($title) && !empty($author)) {
        $insert = "INSERT INTO MentalHealthResource (title, author, type, link, description, is_approved, is_user_submitted, submitted_by, created_at) 
                   VALUES (?, ?, ?, ?, ?, 0, 1, ?, NOW())";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, 'sssssii', $title, $author, $type, $link, $desc, $member_id);
        mysqli_stmt_execute($stmt);
    }
}

$affirmations = [
    "You are stronger than you think. 💪",
    "Your feelings are valid. 💙",
    "You deserve peace and happiness. 🌸",
    "Progress, not perfection. ✨",
    "You are not alone in this. 🤝"
];
$daily_affirmation = $affirmations[array_rand($affirmations)];

$resources_query = "SELECT * FROM MentalHealthResource WHERE is_approved = 1 ORDER BY created_at DESC";
$resources_result = mysqli_query($conn, $resources_query);
$resources = [];
while ($row = mysqli_fetch_assoc($resources_result)) {
    $resources[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Resources - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; }
        .section-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; }
        .affirmation-box { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; font-size: 16px; font-weight: 600; line-height: 1.5; }
        .hotlines { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 15px; }
        .hotline { background: #fadbd8; padding: 12px; border-radius: 6px; border-left: 4px solid #c0392b; }
        .hotline-name { font-weight: 700; color: #c0392b; margin-bottom: 4px; }
        .hotline-number { color: #555; font-size: 13px; }
        .resources-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .resource-card { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #16a085; }
        .resource-title { font-weight: 700; color: #2c3e50; margin-bottom: 6px; }
        .resource-author { color: #95a5a6; font-size: 12px; margin-bottom: 6px; }
        .resource-desc { color: #555; font-size: 13px; line-height: 1.4; margin-bottom: 8px; }
        .resource-link { display: inline-block; padding: 4px 8px; background: #16a085; color: white; text-decoration: none; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .form-section { background: #ecf9f7; padding: 20px; border-radius: 8px; margin-top: 15px; }
        .form-title { font-size: 16px; font-weight: 700; color: #16a085; margin-bottom: 12px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 4px; color: #2c3e50; font-weight: 600; font-size: 13px; }
        input[type="text"], select, textarea { width: 100%; padding: 8px; border: 2px solid #ddd; border-radius: 4px; font-size: 13px; }
        textarea { font-family: 'Segoe UI', sans-serif; min-height: 60px; }
        button { padding: 10px; background: #16a085; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer; font-size: 13px; }
        button:hover { background: #138d75; }
        .empty { text-align: center; color: #95a5a6; padding: 20px; }
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
        <div class="section">
            <div class="section-title">✨ Daily Affirmation</div>
            <div class="affirmation-box"><?php echo $daily_affirmation; ?></div>
        </div>
        
        <div class="section">
            <div class="section-title">🆘 Emergency Hotlines</div>
            <div class="hotlines">
                <div class="hotline">
                    <div class="hotline-name">Kenya Red Cross</div>
                    <div class="hotline-number">1199</div>
                </div>
                <div class="hotline">
                    <div class="hotline-name">Befrienders Kenya</div>
                    <div class="hotline-number">0722-178-177</div>
                </div>
                <div class="hotline">
                    <div class="hotline-name">WHO Mental Health</div>
                    <div class="hotline-number">+41 22 791 21 11</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">📚 Curated Resources</div>
            <?php if (count($resources) > 0): ?>
                <div class="resources-grid">
                    <?php foreach ($resources as $r): ?>
                        <div class="resource-card">
                            <div class="resource-title"><?php echo htmlspecialchars($r['title']); ?></div>
                            <div class="resource-author">By <?php echo htmlspecialchars($r['author']); ?></div>
                            <div class="resource-desc"><?php echo htmlspecialchars($r['description']); ?></div>
                            <?php if (!empty($r['link'])): ?>
                                <a href="<?php echo htmlspecialchars($r['link']); ?>" target="_blank" class="resource-link">Read More →</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty">No resources available yet.</div>
            <?php endif; ?>
            
            <div class="form-section">
                <div class="form-title">+ Suggest a Resource</div>
                <form method="POST">
                    <div class="form-group">
                        <label>Resource Title</label>
                        <input type="text" name="resource_title" required>
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="resource_author" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="resource_type">
                            <option value="article">Article</option>
                            <option value="book">Book</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Link (optional)</label>
                        <input type="text" name="resource_link">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="resource_desc"></textarea>
                    </div>
                    <button type="submit" name="submit_resource">Submit Resource</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>