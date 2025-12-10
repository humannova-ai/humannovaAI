<?php
/**
 * Configuration Brevo SMTP
 * 
 * Pour l'envoi d'emails transactionnels
 */

class BrevoMailer {
    // Configuration SMTP Brevo
    private $smtp_host = 'smtp-relay.brevo.com';
    private $smtp_port = 587;
    private $smtp_user = '9d5eec001@smtp-brevo.com';
    private $smtp_pass = 'djAUzC9sGfWBKIYv';
    
    // Expéditeur par défaut
    private $from_email = 'azazee044@gmail.com';
    private $from_name = 'Human Nova AI';
    
    /**
     * Envoyer un email via SMTP Brevo
     * @param string $to Email du destinataire
     * @param string $toName Nom du destinataire
     * @param string $subject Sujet de l'email
     * @param string $htmlBody Corps HTML de l'email
     * @param string $textBody Corps texte (optionnel)
     * @return array Résultat de l'envoi
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
        
        // Construire le message MIME
        $boundary = md5(time());
        
        // Headers
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        $headers .= "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        // Corps du message
        $message = "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $textBody . "\r\n\r\n";
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $message .= $htmlBody . "\r\n\r\n";
        $message .= "--$boundary--";
        
        // Connexion SMTP
        $socket = @fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 30);
        
        if (!$socket) {
            return ['success' => false, 'message' => "Connexion SMTP échouée: $errstr ($errno)"];
        }
        
        // Lire la réponse de bienvenue
        $response = $this->getResponse($socket);
        
        // EHLO
        $this->sendCommand($socket, "EHLO " . gethostname());
        $response = $this->getResponse($socket);
        
        // STARTTLS
        $this->sendCommand($socket, "STARTTLS");
        $response = $this->getResponse($socket);
        
        // Activer TLS
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        
        // EHLO après TLS
        $this->sendCommand($socket, "EHLO " . gethostname());
        $response = $this->getResponse($socket);
        
        // AUTH LOGIN
        $this->sendCommand($socket, "AUTH LOGIN");
        $response = $this->getResponse($socket);
        
        // Username (base64)
        $this->sendCommand($socket, base64_encode($this->smtp_user));
        $response = $this->getResponse($socket);
        
        // Password (base64)
        $this->sendCommand($socket, base64_encode($this->smtp_pass));
        $response = $this->getResponse($socket);
        
        if (strpos($response, '235') === false && strpos($response, '250') === false) {
            fclose($socket);
            return ['success' => false, 'message' => 'Authentification SMTP échouée'];
        }
        
        // MAIL FROM
        $this->sendCommand($socket, "MAIL FROM:<{$this->from_email}>");
        $response = $this->getResponse($socket);
        
        // RCPT TO
        $this->sendCommand($socket, "RCPT TO:<$to>");
        $response = $this->getResponse($socket);
        
        // DATA
        $this->sendCommand($socket, "DATA");
        $response = $this->getResponse($socket);
        
        // Envoyer les headers et le message
        $emailContent = "To: $toName <$to>\r\n";
        $emailContent .= "Subject: $subject\r\n";
        $emailContent .= $headers;
        $emailContent .= "\r\n";
        $emailContent .= $message;
        $emailContent .= "\r\n.\r\n";
        
        fwrite($socket, $emailContent);
        $response = $this->getResponse($socket);
        
        // QUIT
        $this->sendCommand($socket, "QUIT");
        fclose($socket);
        
        if (strpos($response, '250') !== false || strpos($response, '354') !== false) {
            return ['success' => true, 'message' => 'Email envoyé avec succès à ' . $to];
        }
        
        return ['success' => false, 'message' => 'Erreur lors de l\'envoi: ' . $response];
    }
    
    /**
     * Envoyer une commande SMTP
     */
    private function sendCommand($socket, $command) {
        fwrite($socket, $command . "\r\n");
    }
    
    /**
     * Lire la réponse du serveur SMTP
     */
    private function getResponse($socket) {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
        }
        return $response;
    }
    
    /**
     * Définir l'expéditeur
     */
    public function setFrom($email, $name = '') {
        $this->from_email = $email;
        if ($name) $this->from_name = $name;
    }
}

/**
 * Fonction helper pour envoyer un email facilement
 */
function sendBrevoEmail($to, $toName, $subject, $htmlBody, $textBody = '') {
    $mailer = new BrevoMailer();
    return $mailer->send($to, $toName, $subject, $htmlBody, $textBody);
}
?>
