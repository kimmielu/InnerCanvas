<?php
// ============================================================
// Registration Page (Redesigned)
// File: pages/public/register.php
// Color Scheme: Sky Blue + Chocolate Brown
// ============================================================

require_once '../../includes/auth.php';

$message = "";
$message_type = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $result = registerYouthMember($username, $email, $full_name, $password, $confirm_password);
    
    $message = $result['message'];
    $message_type = $result['success'] ? 'success' : 'error';
    
    if ($result['success']) {
        header("refresh:2;url=login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - InnerCanvas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #4A90E2 0%, #87CEEB 50%, #E8D5C4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .logo {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .form-container {
            padding: 40px 30px;
        }
        
        .motivational {
            text-align: center;
            font-style: italic;
            color: #6B5344;
            margin-bottom: 25px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.success {
            background-color: #D4EDDA;
            color: #155724;
            border: 1px solid #C3E6CB;
        }
        
        .message.error {
            background-color: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E8E8E8;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        
        .password-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4A90E2 0%, #6B5344 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .login-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #EEE;
            margin-top: 20px;
        }
        
        .login-link p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .login-link a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }
        
        .login-link a:hover {
            color: #6B5344;
        }
        
        @media (max-width: 480px) {
            .container {
                border-radius: 15px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .logo {
                font-size: 36px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎨</div>
            <h1>InnerCanvas</h1>
            <p>Create your account</p>
        </div>
        
        <div class="form-container">
            <div class="motivational">
                "It is okay not to be okay" • "Check in with yourself"
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <p class="password-hint">Minimum 8 characters</p>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit">Create Account</button>
            </form>
            
            <div class="login-link">
                <p>Already have an account?</p>
                <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>