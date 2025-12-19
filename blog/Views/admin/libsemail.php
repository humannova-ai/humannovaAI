<?php
// libs/email.php
function sendResetEmail($to, $username, $resetLink) {
    $subject = "Réinitialisation de mot de passe - PRO MANAGE AI";
    
    $message = file_get_contents('templates/reset-password-email.html');
    $message = str_replace('[Admin Name]', $username, $message);
    $message = str_replace('[RESET_LINK]', $resetLink, $message);
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: PRO MANAGE AI <noreply@promanageai.com>' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>