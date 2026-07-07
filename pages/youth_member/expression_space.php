<?php
session_start();
require_once '../../includes/auth.php';

requireLogin();
$user = getCurrentUser();
$member_id = $user['member_id'];

$message = "";
$message_type = "";

// Handle post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publish_post'])) {
    $title = trim($_POST['post_title'] ?? '');
    $content = trim($_POST['post_content'] ?? '');
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
    
    if (!empty($title) && !empty($content)) {
        // In real app, save to database
        $message = "✨ Your story has been shared with the community.";
        $message_type = "success";
    }
}

// Mock posts (in real app, from database)
$posts = [
    [
        'author' => 'Anonymous',
        'title' => 'Finding Light in Darkness',
        'excerpt' => 'Today I realized that depression whispers lies. It told me I was worthless, but one small action proved it wrong.',
        'date' => date('M d, Y', strtotime('-2 days')),
        'resonance' => 42,
        'anonymous' => true
    ],
    [
        'author' => 'Jordan',
        'title' => 'How Cold Water Changed Everything',
        'excerpt' => "I was panicking. My heart was racing. Then I tried the cold water shock and... it actually worked. Here's what happened...",
        'date' => date('M d, Y', strtotime('-1 days')),
        'resonance' => 28,
        'anonymous' => false
    ],
    [
        'author' => 'Alex',
        'title' => 'Anxiety, Then Acceptance',
        'excerpt' => 'For years I fought my anxiety. Then I learned to sit with it. This is my journey from resistance to peace...',
        'date' => date('M d, Y', strtotime('-5 days')),
        'resonance' => 156,
        'anonymous' => false
    ],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expression Space - InnerCanvas</title>
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
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .write-section { background: white; border-radius: 15px; padding: 40px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #4A90E2; }
        .write-title { color: #4A90E2; font-size: 24px; font-weight: 700; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 700; font-size: 14px; }
        input[type="text"], textarea { width: 100%; padding: 12px; border: 2px solid #E8E8E8; border-radius: 8px; font-family: inherit; font-size: 14px; }
        input[type="text"]:focus, textarea:focus { outline: none; border-color: #4A90E2; }
        textarea { min-height: 120px; resize: vertical; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
        .publish-btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .publish-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        .message { padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .message.success { background-color: #D4EDDA; color: #155724; border: 1px solid #C3E6CB; }
        .feed-section { background: white; border-radius: 15px; padding: 40px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); border-top: 5px solid #6B5344; }
        .feed-title { color: #6B5344; font-size: 24px; font-weight: 700; margin-bottom: 30px; }
        .post-card { background: linear-gradient(135deg, #F9F9F9 0%, #FFFFFF 100%); border: 2px solid #E8E8E8; border-radius: 12px; padding: 25px; margin-bottom: 20px; transition: all 0.3s; }
        .post-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(74, 144, 226, 0.1); border-color: #4A90E2; }
        .post-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .post-author { font-weight: 700; color: #333; }
        .post-date { font-size: 12px; color: #999; }
        .post-title { font-size: 18px; font-weight: 700; color: #333; margin-bottom: 10px; }
        .post-excerpt { color: #666; line-height: 1.6; margin-bottom: 15px; font-size: 15px; }
        .post-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #EEE; }
        .resonance { color: #E67E22; font-weight: 700; cursor: pointer; transition: all 0.3s; }
        .resonance:hover { color: #D35400; transform: scale(1.1); }
        .read-more { color: #4A90E2; text-decoration: none; font-weight: 700; font-size: 14px; transition: color 0.3s; }
        .read-more:hover { color: #6B5344; }
        @media (max-width: 768px) { .header-content { flex-direction: column; gap: 15px; text-align: center; } .nav-links { justify-content: center; flex-wrap: wrap; } .write-section { padding: 25px; } .feed-section { padding: 25px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌈 InnerCanvas</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reflection_progress.php">Your Journey</a>
                <a href="expression_space.php">Share</a>
                <a href="resources.php">Resources</a>
                <a href="../../config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="write-section">
            <h2 class="write-title">📝 Share Your Story</h2>
            <p style="color: #666; margin-bottom: 25px;">Tell your truth. Share your journey. Help others feel less alone.</p>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="post_title" placeholder="What's your story about?" required>
                </div>
                
                <div class="form-group">
                    <label>Your Story</label>
                    <textarea name="post_content" placeholder="Share your experience, feelings, what you learned..." required></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="anonymous" name="anonymous">
                    <label for="anonymous" style="margin: 0;">Share anonymously</label>
                </div>
                
                <button type="submit" name="publish_post" class="publish-btn">Publish Your Story</button>
            </form>
        </div>
        
        <div class="feed-section">
            <h2 class="feed-title">💬 Community Stories</h2>
            
            <?php foreach ($posts as $post): ?>
                <div class="post-card">
                    <div class="post-header">
                        <div>
                            <div class="post-author"><?php echo htmlspecialchars($post['author']); ?></div>
                            <div class="post-date"><?php echo htmlspecialchars($post['date']); ?></div>
                        </div>
                    </div>
                    
                    <div class="post-title"><?php echo htmlspecialchars($post['title']); ?></div>
                    <div class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></div>
                    
                    <div class="post-footer">
                        <span class="resonance">❤️ <?php echo $post['resonance']; ?> Resonances</span>
                        <a href="#" class="read-more">Read Full Story →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>