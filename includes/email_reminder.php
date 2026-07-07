<?php
// ============================================================
// InnerCanvas Email Reminder System
// File: includes/email_reminder.php
// Usage: Run via cron job daily at 9 AM
// Example: 0 9 * * * /usr/bin/php /path/to/email_reminder.php
// ============================================================

require_once 'db_connection.php';

// Get all members who haven't checked in today
$query = "
    SELECT ym.member_id, ym.full_name, ym.email
    FROM YouthMember ym
    WHERE ym.is_active = 1
    AND ym.member_id NOT IN (
        SELECT DISTINCT member_id FROM MoodEntry 
        WHERE DATE(created_at) = CURDATE()
    )
    LIMIT 100
";

$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("Email Reminder Error: " . mysqli_error($conn));
    exit;
}

$reminder_emails = [
    "We're missing you! Your mental wellness journey is important.",
    "Quick check-in? How are you feeling today?",
    "Consistency is key. Come back and log your mood today.",
    "Just 2 minutes. Check in with how you're feeling.",
    "You're doing great. One more check-in to keep your streak alive.",
    "Your wellbeing matters. Let's check in today.",
    "Missed you yesterday. Today's a fresh start!",
];

while ($member = mysqli_fetch_assoc($result)) {
    $name = htmlspecialchars($member['full_name']);
    $email = htmlspecialchars($member['email']);
    $subject = "💙 We're Missing You - InnerCanvas";
    
    $random_message = $reminder_emails[array_rand($reminder_emails)];
    
    $html_body = "
    <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; }
                .content { background: white; padding: 30px; border-radius: 10px; margin-top: 20px; }
                .message { font-size: 16px; line-height: 1.6; color: #333; margin-bottom: 20px; }
                .cta-button { display: inline-block; background: #4A90E2; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 700; margin: 20px 0; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #E8E8E8; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌈 InnerCanvas</h1>
                </div>
                <div class='content'>
                    <p>Hi <strong>$name</strong>,</p>
                    <p class='message'>$random_message</p>
                    <p>Your mental wellness journey is important to us. Taking a few moments to check in with how you're feeling can make a real difference.</p>
                    <center>
                        <a href='http://localhost:8081/InnerCanvas/pages/youth_member/dashboard.php' class='cta-button'>Check In Now</a>
                    </center>
                    <p style='margin-top: 30px; color: #666;'>Every small step counts. We believe in you. 💙</p>
                </div>
                <div class='footer'>
                    <p>InnerCanvas © 2024. Your mental wellness matters.</p>
                </div>
            </div>
        </body>
    </html>
    ";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: InnerCanvas <noreply@innercanvas.local>" . "\r\n";
    
    // Send email
    $sent = mail($email, $subject, $html_body, $headers);
    
    if ($sent) {
        error_log("Email sent to: $email");
    } else {
        error_log("Failed to send email to: $email");
    }
}

mysqli_close($conn);
echo "Email reminders sent at " . date('Y-m-d H:i:s') . "\n";
?>