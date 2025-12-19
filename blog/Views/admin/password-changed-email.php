<?php
// password-changed-email.php
// Template d'email pour confirmation de changement de mot de passe

function generatePasswordChangedEmail($adminName, $ipAddress, $userAgent, $changeTime) {
    return "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Password Changed - PRO MANAGE AI</title>
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
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .success-icon {
            font-size: 50px;
            margin-bottom: 20px;
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
            color: #27ae60;
            margin-bottom: 25px;
            font-size: 24px;
            text-align: center;
        }
        
        .greeting {
            color: #555555;
            margin-bottom: 25px;
        }
        
        .confirmation-box {
            background-color: #e8f6f3;
            border: 1px solid #a3e4d7;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .security-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #3498db;
        }
        
        .detail-item {
            margin-bottom: 15px;
            display: flex;
        }
        
        .detail-label {
            font-weight: bold;
            color: #2c3e50;
            min-width: 120px;
        }
        
        .detail-value {
            color: #555555;
            word-break: break-all;
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
        
        .action-required {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            color: #0c5460;
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
            color: #3498db;
            text-decoration: none;
        }
        
        .support-link:hover {
            text-decoration: underline;
        }
        
        .success-message {
            text-align: center;
            padding: 20px;
            background-color: #d5f4e6;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        @media (max-width: 600px) {
            .content {
                padding: 25px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .detail-item {
                flex-direction: column;
            }
            
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            <div class='success-icon'>✅</div>
            <div class='logo'>PRO MANAGE AI</div>
            <div>Password Successfully Changed</div>
        </div>
        
        <div class='content'>
            <h1>Password Update Confirmed</h1>
            
            <div class='greeting'>
                Hello <strong>" . htmlspecialchars($adminName) . "</strong>,
            </div>
            
            <div class='success-message'>
                <strong>✓ Your password has been successfully updated!</strong>
                <p>You can now login with your new password.</p>
            </div>
            
            <div class='confirmation-box'>
                <p>This email confirms that your PRO MANAGE AI Admin Portal password was changed on:</p>
                <p style='text-align: center; font-size: 18px; font-weight: bold; color: #27ae60;'>
                    " . htmlspecialchars($changeTime) . "
                </p>
            </div>
            
            <div class='security-details'>
                <h3 style='color: #3498db; margin-top: 0;'>Security Details</h3>
                
                <div class='detail-item'>
                    <div class='detail-label'>IP Address:</div>
                    <div class='detail-value'>" . htmlspecialchars($ipAddress) . "</div>
                </div>
                
                <div class='detail-item'>
                    <div class='detail-label'>Device/Browser:</div>
                    <div class='detail-value'>" . htmlspecialchars($userAgent) . "</div>
                </div>
                
                <div class='detail-item'>
                    <div class='detail-label'>Change Time:</div>
                    <div class='detail-value'>" . htmlspecialchars($changeTime) . " (UTC)</div>
                </div>
            </div>
            
            <div class='warning-box'>
                <div class='warning-title'>Important Security Notice</div>
                <p>If you did NOT make this change:</p>
                <ul style='margin-left: 20px;'>
                    <li>Immediately reset your password again</li>
                    <li>Contact our security team</li>
                    <li>Review your account activity</li>
                </ul>
            </div>
            
            <div class='action-required'>
                <strong>Next Steps:</strong>
                <ol style='margin-left: 20px;'>
                    <li>Login to your admin account with the new password</li>
                    <li>Update any saved passwords in your browser/device</li>
                    <li>Consider enabling Two-Factor Authentication for added security</li>
                </ol>
            </div>
            
            <p style='margin-top: 30px;'>
                Best regards,<br>
                <strong>The PRO MANAGE AI Security Team</strong>
            </p>
        </div>
        
        <div class='footer'>
            <p>This is an automated security notification from PRO MANAGE AI Admin Portal.</p>
            <p>
                Questions or concerns? 
                <a href='mailto:security@promanageai.com' class='support-link'>Contact Security Team</a>
            </p>
            <p>© " . date('Y') . " PRO MANAGE AI. All rights reserved.</p>
            <p style='font-size: 12px; color: #95a5a6; margin-top: 15px;'>
                For security reasons, please do not reply to this email.
                If you need assistance, contact our support team directly.
            </p>
        </div>
    </div>
</body>
</html>
    ";
}

// Fonction pour envoyer l'email de confirmation
function sendPasswordChangedEmail($toEmail, $adminName, $ipAddress = '', $userAgent = '') {
    // Détails de changement
    $changeTime = date('Y-m-d H:i:s') . ' (UTC)';
    
    // Si IP non fournie, obtenir depuis $_SERVER
    if (empty($ipAddress)) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    // Si User-Agent non fourni
    if (empty($userAgent)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
    
    // Générer le contenu HTML de l'email
    $emailContent = generatePasswordChangedEmail($adminName, $ipAddress, $userAgent, $changeTime);
    
    // Sujet de l'email
    $subject = "Password Successfully Changed - PRO MANAGE AI Admin Portal";
    
    // Headers pour email HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: PRO MANAGE AI Security <security@promanageai.com>" . "\r\n";
    $headers .= "Reply-To: security@promanageai.com" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "X-Priority: 1 (High)" . "\r\n";
    $headers .= "X-MSMail-Priority: High" . "\r\n";
    $headers .= "Importance: High" . "\r\n";
    
    // Envoyer l'email
    $sent = mail($toEmail, $subject, $emailContent, $headers);
    
    return $sent;
}
?>