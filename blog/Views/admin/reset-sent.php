<?php
session_start();
$email = $_SESSION['reset_email'] ?? 'unknown@example.com';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Link Sent</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a; 
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.05);
            padding: 40px 30px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        h1 { margin-bottom: 20px; color: #2ecc71; }
        p { color: #aaa; margin-bottom: 15px; }
        .email { 
            background: rgba(46, 204, 113, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .links { margin-top: 25px; }
        .links a {
            color: #667eea;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>Reset Link Sent!</h1>
        <p>We've sent a password reset link to:</p>
        <div class="email"><?php echo htmlspecialchars($email); ?></div>
        <p>Check your email and follow the instructions.</p>
        
        <div class="links">
            <a href="login.php">← Back to Login</a>
            <a href="index.php">← Back to Main Site</a>
        </div>
    </div>
</body>
</html>