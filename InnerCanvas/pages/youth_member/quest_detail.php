<?php
// ============================================================
// Quest Detail Page
// File: pages/youth_member/quest_detail.php
// Purpose: Show full HOW TO, benefits, and completion message
// ============================================================

session_start();

require_once '../../includes/auth.php';
require_once '../../includes/quest_functions.php';

// Require login
requireLogin();

// Get current user
$user = getCurrentUser();
$member_id = $user['member_id'];

// Get quest ID from URL
$progress_id = isset($_GET['progress_id']) ? (int)$_GET['progress_id'] : null;
$quest_id = isset($_GET['quest_id']) ? (int)$_GET['quest_id'] : null;

if (!$progress_id && !$quest_id) {
    header("Location: sidequests.php");
    exit();
}

// Get quest details (simplified - in real app you'd join with more details)
$quest = null;
if ($quest_id) {
    $quest = getSidequestById($quest_id);
}

// Map quest IDs to detailed instructions
$questDetails = [
    // MOOD 1: PANICKING
    'panic-grounding' => [
        'title' => '5-4-3-2-1 Grounding Technique',
        'howto' => 'Notice 5 things you see, 4 you can touch, 3 you hear, 2 you smell, 1 you taste. Start with sight: look around and name 5 things you see. Then touch: feel 4 different textures. Then hearing: name 3 sounds around you. Then smell: notice 2 scents. Finally taste: put something in your mouth. Go slowly. This grounds you in sensory reality.',
        'benefits' => ['Interrupts panic spiral', 'Brings you to present moment', 'Activates parasympathetic system', 'Works anywhere, anytime'],
        'completion' => '🌟 You did it. You pulled yourself back. That took courage, and you\'re here now, in this moment, safe. Your nervous system heard you. Well done. 💙'
    ],
    'panic-cold-water' => [
        'title' => 'Cold Water Shock Reset',
        'howto' => 'Go to the nearest water source. Splash your face with COLD water, or hold your hands under cold water for 15 seconds. If you can, plunge your face into a bowl of cold water. Come out and breathe slowly. Notice how your body feels different. Cold water triggers your body\'s natural calming response immediately.',
        'benefits' => ['Activates vagus nerve (natural brake pedal)', 'Forces you into present moment', 'Works instantly', 'Safe and physiological'],
        'completion' => '❄️ Breathe. You just reset your entire nervous system. That cold water woke you up to NOW. You\'re grounded, you\'re here, you\'re safe. Excellent work. 💙'
    ],
    'panic-breathing' => [
        'title' => 'Box Breathing (1-Minute Version)',
        'howto' => 'Find a quiet spot. BREATHE IN for 4 counts. HOLD for 4 counts. BREATHE OUT for 4 counts. HOLD for 4 counts. Repeat this pattern 5 times. That\'s 2 minutes total. Feel your breath slow. Feel your nervous system calm. When you control your breath, you control your panic.',
        'benefits' => ['Activates parasympathetic response', 'Gives panic-brain simple task', 'No equipment needed', 'Proven military technique'],
        'completion' => '🌬️ Breathe in. Hold. Breathe out. Hold. You just regulated your own nervous system. That breath control? That was pure power. You\'re calm now. You did that. 💙'
    ],
    
    // MOOD 2: ANXIOUS
    'anxiety-worry-container' => [
        'title' => 'Worry Container Ritual',
        'howto' => 'Get paper and a container (jar, box, envelope, cup - anything that can hold). Write down EVERY worry on separate papers. No filtering, no organizing. Just dump them out: "Will I fail?" "Does my friend hate me?" "What if I\'m not good enough?" Fold each paper. Place them in the container. Close it. Say out loud: "These worries are here. And I am choosing to contain them right now. I don\'t have to solve them tonight." Put the container somewhere you\'ll see it. This is the ritual that says: worry, you don\'t own my whole mind.',
        'benefits' => ['Externalizes anxiety', 'Gives worry a physical home', 'Reduces rumination', 'Ritual itself is calming'],
        'completion' => '📦 You just containerized your worries. They\'re real, they\'re there, and you\'ve decided they don\'t get to run your whole night. That\'s power. That\'s wisdom. Rest now. 💙'
    ],
    'anxiety-worst-case' => [
        'title' => 'Worst-Case Scenario Journal',
        'howto' => 'Write down your biggest worry (be specific about the situation). Now write out the absolute worst-case scenario in detail. Don\'t hold back. "I fail the test. I get an F. My GPA drops. I can\'t get into college. I become a complete failure." Now ask yourself: "And then what? How would I survive it?" Write the answer. "I\'d retake the class. Ask for tutoring. Reapply next year. Find another path. Life doesn\'t end." Realize: you\'ve survived worse. You\'re resourceful. Repeat for other catastrophes you imagine.',
        'benefits' => ['Tests reality of worst-case', 'Proves you\'re resilient', 'Separates scary thought from danger', 'Prevents avoidance'],
        'completion' => '🎯 You just looked your worst fear in the face. And you realized: You\'re strong enough to handle it. Even if it happened, you\'d survive. You\'d adapt. You\'d continue. That\'s not bravery — that\'s truth. 💙'
    ],
    
    // MOOD 3: DEPRESSED
    'depression-micro-victories' => [
        'title' => 'Micro Victories Log',
        'howto' => 'Get pen and paper or open notes app. Write down 5 tiny things you did today. It doesn\'t matter how small: "Got out of bed," "Drank water," "Showered," "Texted a friend," "Ate something." These are your victories. Depression says you did nothing. This list proves you did something. Five somethings. Tomorrow, do it again.',
        'benefits' => ['Combats depression\'s core lie', 'Builds momentum', 'Visible evidence of progress', 'Trains brain to notice positive'],
        'completion' => '⭐ Look at you. You showed up. Five times today, you chose yourself. That\'s not nothing. That\'s everything. 💙'
    ],
    'depression-comfort-box' => [
        'title' => 'Sensory Comfort Box',
        'howto' => 'Find a box or container. Gather items that feel good to your senses. Something soft: soft blanket, socks, stuffed animal. Something that smells good: candle, essential oil, lotion. Something you like looking at: photo, art, colored paper. Something good to taste: chocolate, tea, gum. Something to fidget with: stress ball, putty, worry stone. Decorate the box if you want. Keep it accessible. On bad days, open it. Touch something soft. Light the candle. Smell it. Taste something. Feel something. Depression kills motivation. This box removes the decision-making. It just says: "Here. Feel something good."',
        'benefits' => ['Makes self-care effortless', 'Engages all senses', 'Gives you something to DO', 'Creates hope ritual'],
        'completion' => '🎁 You created your own hope box. When depression says "nothing feels good," you have proof that good things exist. That\'s resistance. That\'s revolution. 💙'
    ],
    
    // MOOD 4: DISASSOCIATED
    'disso-cold-water' => [
        'title' => 'Cold Water Shock',
        'howto' => 'Go to the nearest water source. Splash your face with COLD water or hold your hands under cold water for 15 seconds. You can even jump in a cold shower for 30 seconds if you\'re brave. Come out. Breathe. Notice: you\'re back. Cold is a physical interrupt. Your body can\'t be numb when it\'s shocked awake. You feel it. You\'re alive.',
        'benefits' => ['Activates vagus nerve', 'Forces you into present moment', 'Safe and works instantly', 'Physiological response'],
        'completion' => '❄️ Cold brought you back. Your body just screamed ALIVE. Welcome home to your skin. 💙'
    ],
    'disso-texture' => [
        'title' => 'Texture Exploration Quest',
        'howto' => 'Gather 5 items with different textures: silk scarf (smooth), sandpaper (rough), ice cube (cold), velvet (plush), rubber (flexible). Close your eyes. Pick up one item. Feel it. Really feel it. Notice every sensation: temperature, texture, how it moves, how your skin responds. Spend 2 minutes per texture. Open your eyes. You\'re present again. Dissociation = disconnect from body. Textures = reconnect to body.',
        'benefits' => ['Grounds in physical sensation', 'Safe and non-invasive', 'Activates sensory cortex', 'Calming ritual'],
        'completion' => '🖐️ You felt everything. Your skin remembered it\'s alive. Your body is home. You\'re back. 💙'
    ],
    
    // MOOD 5: BRAIN FOG
    'brainfog-movement' => [
        'title' => 'Movement Break Shuffle',
        'howto' => 'Stand up immediately. Do 1-2 minutes of movement: jump jacks, high knees, dancing, arm circles, anything that gets your heart rate up. Move hard. Get blood flowing. Sit back down. Notice: brain is clearer. Fog lifted. Movement increases blood flow to brain. Blood flow = clarity. It\'s that simple.',
        'benefits' => ['Immediate focus boost', 'No equipment needed', 'Proven ADHD strategy', 'Energizing'],
        'completion' => '🏃 You moved the fog away. Your brain is waking up. Focus is returning. You did that. 💙'
    ],
    'brainfog-pomodoro' => [
        'title' => 'Pomodoro Power Session',
        'howto' => 'Set timer for 25 minutes. Choose ONE task only (no multitasking, no switching). Work intensely on that one thing for 25 minutes. Timer goes off → take 5 minute break. Move, stretch, drink water, rest. Then do another 25-minute session. Do 2-3 cycles. Your brain can focus. It just needs structure and regular breaks. This proves you CAN concentrate.',
        'benefits' => ['Proven focus technique', 'Built-in breaks prevent burnout', 'Time-bounded (not overwhelming)', 'Shows you can concentrate'],
        'completion' => '⏱️ You just proved you can concentrate. 25 mins of pure focus. That\'s power. Do it again tomorrow. 💙'
    ],
    
    // MOOD 6: CREATIVE
    'creative-doodle' => [
        'title' => 'Doodle Meditation Freestyle',
        'howto' => 'Get pen and paper. Put on music. Doodle for 15 minutes with NO goal. Don\'t try to make it "good." Don\'t plan what to draw. Just let your hand move. Follow what feels right. Abstract, messy, beautiful - all welcome. Let your hands think. When done, look at what you created. That\'s your creativity speaking.',
        'benefits' => ['Bypasses perfectionism', 'Meditative flow state', 'Creates without pressure', 'Surprising what emerges'],
        'completion' => '🎨 You doodled yourself into flow. What came out? That\'s your creativity speaking. Listen to it. 💙'
    ],
    
    // MOOD 7: HAPPY
    'happy-dance' => [
        'title' => 'Dance Celebration Party',
        'howto' => 'Pick 3 of your favorite songs. Press play. Stand up. DANCE. No choreography. No judgment. Pure celebration. Big movements. Wild dancing. Let the joy move through you. Dance like no one\'s watching. Dance like you OWN this moment. When the songs end, notice: you\'re glowing.',
        'benefits' => ['Amplifies positive mood', 'Creates endorphin spike', 'Embodied joy (not just thinking)', 'Fun as medicine'],
        'completion' => '💃 You just celebrated yourself. That dance was pure joy. Hold onto that feeling. You earned it. 💙'
    ],
];

// Get the quest key (simplified - in real app this would be more dynamic)
$questKey = isset($_GET['quest']) ? $_GET['quest'] : 'panic-grounding';
$details = isset($questDetails[$questKey]) ? $questDetails[$questKey] : $questDetails['panic-grounding'];

// Handle completion
$completed = false;
$completionMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    $completed = true;
    $completionMessage = $details['completion'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($details['title']); ?> - InnerCanvas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #87CEEB 0%, #F5F5F5 100%);
            color: #333;
        }
        
        header {
            background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%);
            color: white;
            padding: 20px;
        }
        
        .header-content {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 15px;
            display: inline-block;
        }
        
        .back-btn:hover {
            text-decoration: underline;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .quest-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        h1 {
            color: #4A90E2;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .section {
            margin: 30px 0;
        }
        
        .section-title {
            color: #6B5344;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            border-bottom: 3px solid #4A90E2;
            padding-bottom: 10px;
        }
        
        .howto-content {
            background: #F9F9F9;
            padding: 20px;
            border-radius: 10px;
            line-height: 1.8;
            font-size: 15px;
            color: #555;
        }
        
        .benefits-list {
            list-style: none;
        }
        
        .benefits-list li {
            padding: 10px 0;
            padding-left: 30px;
            position: relative;
            font-size: 15px;
        }
        
        .benefits-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #2ECC71;
            font-weight: bold;
            font-size: 18px;
        }
        
        .completion-section {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-top: 30px;
        }
        
        .completion-section h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .completion-message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .completion-btn {
            background: white;
            color: #2ECC71;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .completion-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .success-message {
            background: linear-gradient(135deg, #2ECC71 0%, #27AE60 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            font-size: 18px;
            line-height: 1.8;
        }
        
        .success-message h2 {
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: white;
            color: #2ECC71;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .quest-card {
                padding: 20px;
            }
            
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="sidequests.php" class="back-btn">← Back to Sidequests</a>
        </div>
    </header>
    
    <div class="container">
        <?php if ($completed): ?>
            <!-- COMPLETION SUCCESS -->
            <div class="success-message">
                <h2>🎉 You Did It!</h2>
                <div style="font-size: 24px; margin: 20px 0;">
                    <?php echo nl2br(htmlspecialchars($completionMessage)); ?>
                </div>
                <a href="sidequests.php" class="back-link">Continue to More Quests</a>
            </div>
        <?php else: ?>
            <!-- QUEST DETAILS -->
            <div class="quest-card">
                <a href="sidequests.php" class="back-btn" style="color: #4A90E2; display: block; margin-bottom: 20px;">← Back</a>
                
                <h1><?php echo htmlspecialchars($details['title']); ?></h1>
                
                <!-- HOW TO DO IT -->
                <div class="section">
                    <div class="section-title">📖 How To Do This</div>
                    <div class="howto-content">
                        <?php echo nl2br(htmlspecialchars($details['howto'])); ?>
                    </div>
                </div>
                
                <!-- BENEFITS -->
                <div class="section">
                    <div class="section-title">💡 Benefits</div>
                    <ul class="benefits-list">
                        <?php foreach ($details['benefits'] as $benefit): ?>
                            <li><?php echo htmlspecialchars($benefit); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <!-- COMPLETION BUTTON -->
                <div class="completion-section">
                    <h3>Ready to Complete This Quest?</h3>
                    <p style="margin-bottom: 20px; font-size: 14px;">
                        Once you finish the activity, come back and mark it complete. <br>
                        You'll get your personalized completion message. 🎯
                    </p>
                    <form method="POST" action="">
                        <button type="submit" name="mark_complete" class="completion-btn">I Completed This Quest ✓</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>