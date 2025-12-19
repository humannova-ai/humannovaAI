<?php
// reset-password-email.php
// Template d'email pour la réinitialisation du mot de passe

function generateResetEmail($adminName, $resetLink, $expiryTime = '1 hour') {
    return "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Password Reset - PRO MANAGE AI</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 40px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 24px;
        }
        
        .greeting {
            color: #555555;
            margin-bottom: 25px;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 30px 0;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .link-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #667eea;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            color: #555555;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .warning-title {
            color: #856404;
            font-weight: bold;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .warning-title:before {
            content: '⚠️';
        }
        
        .info-box {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            color: #0c5460;
        }
        
        .steps {
            margin: 25px 0;
        }
        
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .step-number {
            background: #667eea;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .footer {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-size: 14px;
            border-top: 1px solid #eeeeee;
            background-color: #f8f9fa;
        }
        
        .support-link {
            color: #667eea;
            text-decoration: none;
        }
        
        .support-link:hover {
            text-decoration: underline;
        }
        
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        
        .expiry-notice {
            color: #e74c3c;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #fdf2f2;
            border-radius: 6px;
        }
        
        @media (max-width: 600px) {
            .content {
                padding: 25px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .reset-button {
                padding: 14px 30px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <div class='logo'>PRO MANAGE AI</div>
            <div>Admin Portal Security</div>
        </div>
        
        <div class='content'>
            <h1>Password Reset Request</h1>
            
            <div class='greeting'>
                Hello <strong>" . htmlspecialchars($adminName) . "</strong>,
            </div>
            
            <p>We received a request to reset your password for the PRO MANAGE AI Admin Portal.</p>
            
            <div class='expiry-notice'>
                ⚠️ This reset link will expire in $expiryTime
            </div>
            
            <div class='button-container'>
                <a href='" . htmlspecialchars($resetLink) . "' class='reset-button'>
                    Reset Your Password
                </a>
            </div>
            
            <p>If the button above doesn't work, copy and paste the following link into your browser:</p>
            
            <div class='link-container'>
                " . htmlspecialchars($resetLink) . "
            </div>
            
            <div class='steps'>
                <div class='step'>
                    <div class='step-number'>1</div>
                    <div>Click the reset button or copy the link above</div>
                </div>
                <div class='step'>
                    <div class='step-number'>2</div>
                    <div>Create a new strong password</div>
                </div>
                <div class='step'>
                    <div class='step-number'>3</div>
                    <div>Confirm your new password</div>
                </div>
                <div class='step'>
                    <div class='step-number'>4</div>
                    <div>Login with your new credentials</div>
                </div>
            </div>
            
            <div class='warning-box'>
                <div class='warning-title'>Important Security Notice</div>
                <p>If you didn't request this password reset, please:</p>
                <ul style='margin-left: 20px;'>
                    <li>Ignore this email</li>
                    <li>Contact our support team immediately</li>
                    <li>Consider changing your email password</li>
                </ul>
            </div>
            
            <div class='info-box'>
                <strong>Need help?</strong>
                <p>If you're having trouble resetting your password or have any questions about your account security, please contact our support team.</p>
            </div>
            
            <p style='margin-top: 30px;'>
                Best regards,<br>
                <strong>The PRO MANAGE AI Security Team</strong>
            </p>
        </div>
        
        <div class='footer'>
            <p>This is an automated security message from PRO MANAGE AI Admin Portal.</p>
            <p>
                Need assistance? 
                <a href='mailto:support@promanageai.com' class='support-link'>Contact Support</a>
            </p>
            <p>© " . date('Y') . " PRO MANAGE AI. All rights reserved.</p>
            <p style='font-size: 12px; color: #95a5a6; margin-top: 15px;'>
                For your security, this email was sent to you because a password reset was requested for your account.
                If you believe this was sent in error, please disregard this message.
            </p>
        </div>
    </div>
</body>
</html>
    ";
}

// Fonction pour envoyer l'email
function sendResetPasswordEmail($toEmail, $adminName, $resetToken) {
    // Générer le lien de réinitialisation
    $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/admin/reset-password.php?token=" . $resetToken;
    
    // Générer le contenu HTML de l'email
    $emailContent = generateResetEmail($adminName, $resetLink);
    
    // Sujet de l'email
    $subject = "Password Reset Request - PRO MANAGE AI Admin Portal";
    
    // Headers pour email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: PRO MANAGE AI Security <security@promanageai.com>" . "\r\n";
    $headers .= "Reply-To: support@promanageai.com" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1 (High)" . "\r\n";
    $headers .= "X-MSMail-Priority: High" . "\r\n";
    $headers .= "Importance: High" . "\r\n";
    
    // Envoyer l'email
    $sent = mail($toEmail, $subject, $emailContent, $headers);
    
    return $sent;
}
?>