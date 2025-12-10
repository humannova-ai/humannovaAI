<?php
/**
 * Configuration Email avec Brevo API
 *
 * Pour l'envoi d'emails transactionnels
 */

class EmailSender {
    // Configuration Brevo API
    private $api_key = 'xkeysib-d93f7e8c4b2a1f5e6d8c9b0a7e3f2d1c4b5a6e7f8d9c0b1a2e3f4d5c6b7a8e9f-XXXXXXXX';
    private $api_url = 'https://api.brevo.com/v3/smtp/email';
    
    // Configuration SMTP Brevo (fallback)
    private $smtp_host = 'smtp-relay.brevo.com';
    private $smtp_port = 587;
    private $smtp_user = '9d5eec001@smtp-brevo.com';
    private $smtp_pass = 'djAUzC9sGfWBKIYv';
    
    // Expéditeur par défaut
    private $from_email = 'azazee044@gmail.com';
    private $from_name = 'Human Nova AI';
    
    /**
     * Envoyer un email via PHP mail() avec SMTP config
     */
    public function send($to, $toName, $subject, $htmlBody, $textBody = '') {
        // Valider l'email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide: ' . $to];
        }
        
        // Si pas de texte, créer une version texte simple
        if (empty($textBody)) {
            $textBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));
        }
        
        // Essayer d'abord avec SMTP direct
        $result = $this->sendViaSMTP($to, $toName, $subject, $htmlBody, $textBody);
        
        if ($result['success']) {
            return $result;
        }
        
        // Fallback vers mail() PHP
        return $this->sendViaMail($to, $toName, $subject, $htmlBody, $textBody);
    }
    
    /**
     * Envoi via SMTP Brevo
     */
    private function sendViaSMTP($to, $toName, $subject, $htmlBody, $textBody) {
        $errno = 0;
        $errstr = '';
        
        // Essayer la connexion SMTP
        $socket = @fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 15);
        
        if (!$socket) {
            return ['success' => false, 'message' => "Connexion SMTP échouée: $errstr"];
        }
        
        try {
            // Construire le message
            $boundary = md5(uniqid(time()));
            
            // Lire réponse bienvenue
            $this->getResponse($socket);
            
            // EHLO
            fwrite($socket, "EHLO localhost\r\n");
            $this->getResponse($socket);
            
            // STARTTLS
            fwrite($socket, "STARTTLS\r\n");
            $response = $this->getResponse($socket);
            
            if (strpos($response, '220') !== false) {
                // Activer TLS
                if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                    fclose($socket);
                    return ['success' => false, 'message' => 'TLS échoué'];
                }
                
                // EHLO après TLS
                fwrite($socket, "EHLO localhost\r\n");
                $this->getResponse($socket);
            }
            
            // AUTH LOGIN
            fwrite($socket, "AUTH LOGIN\r\n");
            $this->getResponse($socket);
            
            // Username
            fwrite($socket, base64_encode($this->smtp_user) . "\r\n");
            $this->getResponse($socket);
            
            // Password
            fwrite($socket, base64_encode($this->smtp_pass) . "\r\n");
            $response = $this->getResponse($socket);
            
            if (strpos($response, '235') === false) {
                fclose($socket);
                return ['success' => false, 'message' => 'Authentification échouée'];
            }
            
            // MAIL FROM
            fwrite($socket, "MAIL FROM:<{$this->from_email}>\r\n");
            $this->getResponse($socket);
            
            // RCPT TO
            fwrite($socket, "RCPT TO:<{$to}>\r\n");
            $response = $this->getResponse($socket);
            
            if (strpos($response, '250') === false) {
                fclose($socket);
                return ['success' => false, 'message' => 'Destinataire refusé'];
            }
            
            // DATA
            fwrite($socket, "DATA\r\n");
            $this->getResponse($socket);
            
            // Email content
            $email = "Date: " . date('r') . "\r\n";
            $email .= "From: {$this->from_name} <{$this->from_email}>\r\n";
            $email .= "To: {$toName} <{$to}>\r\n";
            $email .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $email .= "MIME-Version: 1.0\r\n";
            $email .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $email .= "\r\n";
            $email .= "--{$boundary}\r\n";
            $email .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $email .= $textBody . "\r\n";
            $email .= "--{$boundary}\r\n";
            $email .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $email .= $htmlBody . "\r\n";
            $email .= "--{$boundary}--\r\n";
            $email .= ".\r\n";
            
            fwrite($socket, $email);
            $response = $this->getResponse($socket);
            
            // QUIT
            fwrite($socket, "QUIT\r\n");
            fclose($socket);
            
            if (strpos($response, '250') !== false) {
                return ['success' => true, 'message' => 'Email envoyé avec succès à ' . $to];
            }
            
            return ['success' => false, 'message' => 'Erreur envoi: ' . $response];
            
        } catch (Exception $e) {
            if ($socket) fclose($socket);
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }
    
    /**
     * Fallback: Envoi via mail() PHP
     */
    private function sendViaMail($to, $toName, $subject, $htmlBody, $textBody) {
        $boundary = md5(uniqid(time()));
        
        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
        $body .= $textBody . "\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $body .= $htmlBody . "\r\n";
        $body .= "--{$boundary}--";
        
        $sent = @mail($to, "=?UTF-8?B?" . base64_encode($subject) . "?=", $body, $headers);
        
        if ($sent) {
            return ['success' => true, 'message' => 'Email envoyé à ' . $to];
        }
        
        return ['success' => false, 'message' => 'Échec de l\'envoi via mail()'];
    }
    
    /**
     * Lire réponse SMTP
     */
    private function getResponse($socket) {
        $response = '';
        stream_set_timeout($socket, 10);
        while ($line = @fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] == ' ') break;
            $info = stream_get_meta_data($socket);
            if ($info['timed_out']) break;
        }
        return $response;
    }
    
    /**
     * Créer le template HTML pour les emails de statut
     */
    public static function createStatusEmailTemplate($userName, $eventTitle, $status) {
        $statusText = $status === 'approuve' ? 'APPROUVÉE' : 'REFUSÉE';
        $statusColor = $status === 'approuve' ? '#00ff88' : '#ff3333';
        $statusMessage = $status === 'approuve' 
            ? 'Félicitations ! Votre participation a été approuvée. Nous avons hâte de vous voir !'
            : 'Malheureusement, votre participation n\'a pas été retenue cette fois-ci. N\'hésitez pas à participer à nos prochains événements.';
        
        return "
<!DOCTYPE html>
<html>
<head><meta charset='UTF-8'></head>
<body style='font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px;'>
    <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;'>
            <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>HUMAN NOVA AI</h1>
        </div>
        <div style='padding: 40px;'>
            <h2 style='color: #333; margin: 0 0 20px 0;'>Bonjour {$userName},</h2>
            <p style='color: #666; font-size: 16px; line-height: 1.6;'>Nous avons une mise à jour concernant votre participation :</p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <span style='display: inline-block; padding: 15px 40px; background: {$statusColor}; color: #fff; font-size: 18px; font-weight: bold; border-radius: 30px;'>{$statusText}</span>
            </div>
            
            <div style='background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 20px 0; border-radius: 0 8px 8px 0;'>
                <strong style='color: #333;'>Événement:</strong><br>
                <span style='color: #667eea; font-size: 18px;'>{$eventTitle}</span>
            </div>
            
            <p style='color: #666; font-size: 16px; line-height: 1.6;'>{$statusMessage}</p>
        </div>
        <div style='background: #f8f9fa; padding: 20px; text-align: center; color: #999; font-size: 13px;'>
            <p style='margin: 0;'>© 2025 Human Nova AI. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>";
    }
}
?>
