<?php
// ============================================================
// Login Page (Redesigned)
// File: pages/public/login.php
// Color Scheme: Sky Blue + Chocolate Brown
// ============================================================

require_once '../../includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: ../youth_member/dashboard.php");
    exit();
}

$message = "";
$message_type = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $result = loginYouthMember($username, $password);
    
    if ($result['success']) {
        header("Location: ../youth_member/dashboard.php");
        exit();
    } else {
        $message = $result['message'];
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InnerCanvas</title>
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
            max-width: 420px;
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
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .message.error {
            background-color: #F8D7DA;
            color: #721C24;
            border: 1px solid #F5C6CB;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
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
        input[type="password"]:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
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
            margin-top: 10px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }
        
        button:active {
            transform: translateY(0);
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            font-size: 12px;
            color: #999;
        }
        
        .register-link {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #EEE;
        }
        
        .register-link p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .register-link a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s;
        }
        
        .register-link a:hover {
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
            <p>Welcome back</p>
        </div>
        
        <div class="form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo htmlspecialchars($message_type); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit">Login</button>
            </form>
            
            <div class="register-link">
                <p>Don't have an account?</p>
                <a href="register.php">Create one now</a>
            </div>
        </div>
    </div>
</body>
</html>