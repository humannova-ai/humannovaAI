<?php
class EmailHelper {
    
    // Configuration email en dur (vous pouvez la déplacer ailleurs si besoin)
    private static $SMTP_FROM_EMAIL = 'votre-email@gmail.com'; // ⚠️ CHANGEZ ICI
    private static $SMTP_FROM_NAME = 'PRO MANAGE AI';
    
    public static function sendPasswordResetEmail($toEmail, $toName, $resetToken) {
        // Construire le lien de réinitialisation
        $resetLink = "http://localhost/blog/index.php?controller=admin&action=resetPassword&token=" . urlencode($resetToken);
        
        $subject = "Réinitialisation de votre mot de passe - PRO MANAGE AI";
        
        // Message HTML
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { 
                    font-family: 'Segoe UI', Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: #ffffff;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    padding: 40px 30px; 
                    text-align: center;
                }
                .header h1 { margin: 0; font-size: 28px; }
                .content { padding: 40px 30px; }
                .content p { margin: 15px 0; color: #555; }
                .button-container { text-align: center; margin: 30px 0; }
                .button { 
                    display: inline-block; 
                    padding: 15px 40px; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white !important; 
                    text-decoration: none; 
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 16px;
                }
                .warning-box {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 4px;
                }
                .link-box {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    word-break: break-all;
                    font-size: 12px;
                    color: #666;
                    margin: 20px 0;
                }
                .footer { 
                    text-align: center; 
                    padding: 20px 30px;
                    background: #f8f9fa;
                    color: #999;
                    font-size: 13px;
                    border-top: 1px solid #e9ecef;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 PRO MANAGE AI</h1>
                    <p style='margin: 10px 0 0 0; font-size: 16px;'>Réinitialisation de mot de passe</p>
                </div>
                
                <div class='content'>
                    <p><strong>Bonjour " . htmlspecialchars($toName) . ",</strong></p>
                    
                    <p>Vous avez demandé à réinitialiser votre mot de passe pour votre compte administrateur PRO MANAGE AI.</p>
                    
                    <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
                    
                    <div class='button-container'>
                        <a href='" . htmlspecialchars($resetLink) . "' class='button'>Réinitialiser mon mot de passe</a>
                    </div>
                    
                    <div class='warning-box'>
                        <strong>⏱️ Important :</strong> Ce lien est valide pendant <strong>1 heure</strong> uniquement.
                    </div>
                    
                    <p><strong>Vous n'avez pas demandé cette réinitialisation ?</strong></p>
                    <p>Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>
                    
                    <p style='margin-top: 30px;'><strong>Lien de réinitialisation :</strong></p>
                    <div class='link-box'>
                        " . htmlspecialchars($resetLink) . "
                    </div>
                </div>
                
                <div class='footer'>
                    <p>&copy; 2024-2025 PRO MANAGE AI. Tous droits réservés.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Headers pour email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . self::$SMTP_FROM_NAME . " <" . self::$SMTP_FROM_EMAIL . ">" . "\r\n";
        $headers .= "Reply-To: " . self::$SMTP_FROM_EMAIL . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        // Envoyer l'email
        return mail($toEmail, $subject, $message, $headers);
    }
}
?>