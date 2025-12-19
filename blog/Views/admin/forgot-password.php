<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PRO MANAGE AI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            max-width: 450px;
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            padding: 50px 40px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .logo h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        h2 { margin: 30px 0; color: #ffffff; }
        
        .message {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
            line-height: 1.5;
        }
        
        .success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .info {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }
        
        input {
            width: 100%;
            padding: 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            margin-bottom: 20px;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.5);
        }
        
        .link {
            color: #667eea;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            font-size: 14px;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>PRO MANAGE AI</h1>
            <p style="color: #a0a0a0;">Admin Portal</p>
        </div>
        
        <div style="font-size: 3rem; margin: 20px 0;">🔑</div>
        <h2>Forgot Password</h2>
        
        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php else: ?>
            <div class="message info">
                📧 Enter your email address and we'll send you a link to reset your password.
            </div>
        <?php endif; ?>
        
        <?php if ($messageType !== 'success'): ?>
        <form method="post" action="">
            <input type="email" name="email" required placeholder="admin@promanageai.com">
            <button type="submit">Send Reset Link</button>
        </form>
        <?php endif; ?>
        
        <a href="index.php?controller=admin&action=login" class="link">← Back to Login</a>
        <a href="index.php" class="link">← Back to Main Site</a>
    </div>
</body>
</html>