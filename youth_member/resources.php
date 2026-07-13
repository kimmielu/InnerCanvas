<?php
session_start();
require_once dirname(__DIR__) . '/config/db_connection.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();
if ($user['admin_role'] !== 'none') header("Location: ../admin/admin_dashboard.php");

$member_id = $user['member_id'];

$affirmations = [
    "You are stronger than you think.",
    "Your feelings are valid and important.",
    "You deserve love and kindness, especially from yourself.",
    "Progress, not perfection, is the goal.",
    "Every moment is a new opportunity to choose peace.",
    "You are not alone in this journey.",
    "Your struggles have made you resilient.",
    "Healing is not linear, and that's okay.",
    "You are capable of creating the life you want.",
    "Small steps forward are still steps forward."
];

$affirmation = $affirmations[array_rand($affirmations)];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_resource'])) {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    
    if (!empty($title) && !empty($type)) {
        $insert = "INSERT INTO MentalHealthResource (title, author, type, link, description, is_approved, is_user_submitted, submitted_by) 
                   VALUES (?, ?, ?, ?, ?, 0, 1, ?)";
        $stmt = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt, 'sssssi', $title, $author, $type, $link, $desc, $member_id);
        mysqli_stmt_execute($stmt);
    }
}

$resources_query = "SELECT * FROM MentalHealthResource WHERE is_approved = 1 ORDER BY resource_id DESC";
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
        .affirmation { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; text-align: center; }
        .affirmation-text { font-size: 16px; font-style: italic; line-height: 1.6; }
        .section { background: white; padding: 25px; border-radius: 12px; margin-bottom: 25px; }
        .section-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 15px; }
        .hotlines { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .hotline { background: #fadbd8; padding: 15px; border-radius: 8px; border-left: 4px solid #c0392b; }
        .hotline-name { font-weight: 700; color: #2c3e50; margin-bottom: 5px; }
        .hotline-number { color: #c0392b; font-weight: 700; }
        .resources-list { list-style: none; }
        .resource-item { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 12px; }
        .resource-title { font-weight: 700; color: #2c3e50; }
        .resource-author { color: #95a5a6; font-size: 12px; }
        .resource-type { display: inline-block; background: #16a085; color: white; padding: 2px 6px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-top: 5px; }
        .resource-desc { color: #555; font-size: 13px; line-height: 1.5; margin-top: 8px; }
        .form-group { margin-bottom: 12px; }
        label { display: block; margin-bottom: 5px; color: #2c3e50; font-weight: 600; font-size: 13px; }
        input[type="text"], select, textarea { width: 100%; padding: 8px; border: 2px solid #ecf0f1; border-radius: 6px; font-family: 'Segoe UI', sans-serif; }
        textarea { min-height: 80px; }
        button { width: 100%; padding: 10px; background: #16a085; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; }
        button:hover { background: #138d75; }
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
        <div class="affirmation">
            <div class="affirmation-text">"<?php echo $affirmation; ?>"</div>
        </div>
        
        <div class="section">
            <div class="section-title">🆘 Emergency Hotlines</div>
            <div class="hotlines">
                <div class="hotline">
                    <div class="hotline-name">National Suicide Prevention Lifeline</div>
                    <div class="hotline-number">1-800-273-8255</div>
                </div>
                <div class="hotline">
                    <div class="hotline-name">Crisis Text Line</div>
                    <div class="hotline-number">Text HOME to 741741</div>
                </div>
                <div class="hotline">
                    <div class="hotline-name">SAMHSA National Helpline</div>
                    <div class="hotline-number">1-800-662-4357</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">📚 Curated Resources</div>
            <ul class="resources-list">
                <?php foreach ($resources as $r): ?>
                    <li class="resource-item">
                        <div class="resource-title"><?php echo htmlspecialchars($r['title']); ?></div>
                        <div class="resource-author">by <?php echo htmlspecialchars($r['author']); ?></div>
                        <span class="resource-type"><?php echo ucfirst($r['type']); ?></span>
                        <div class="resource-desc"><?php echo htmlspecialchars($r['description']); ?></div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="section">
            <div class="section-title">💡 Recommend a Resource</div>
            <form method="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" placeholder="e.g., Atomic Habits" required>
                </div>
                <div class="form-group">
                    <label>Author</label>
                    <input type="text" name="author" placeholder="Author name">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" required>
                        <option value="">Select type</option>
                        <option value="article">Article</option>
                        <option value="book">Book</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Link (optional)</label>
                    <input type="text" name="link" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Why do you recommend this?"></textarea>
                </div>
                <button type="submit" name="submit_resource">Submit Recommendation</button>
            </form>
        </div>
    </div>
</body>
</html>