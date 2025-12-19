<?php
// update-password.php
session_start();
require_once 'Core/connection.php';
require_once 'reset-password-email.php';
require_once 'password-changed-email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation basique
    if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
        header('Location: reset-password.php?token=' . $token . '&error=' . urlencode('All fields are required'));
        exit();
    }
    
    if ($newPassword !== $confirmPassword) {
        header('Location: reset-password.php?token=' . $token . '&error=' . urlencode('Passwords do not match'));
        exit();
    }
    
    // Validation de la force du mot de passe
    if (strlen($newPassword) < 8) {
        header('Location: reset-password.php?token=' . $token . '&error=' . urlencode('Password must be at least 8 characters'));
        exit();
    }
    
    // Vérifier la complexité du mot de passe
    if (!preg_match('/[A-Z]/', $newPassword) || 
        !preg_match('/[a-z]/', $newPassword) || 
        !preg_match('/[0-9]/', $newPassword)) {
        header('Location: reset-password.php?token=' . $token . '&error=' . urlencode('Password must contain uppercase, lowercase, and numbers'));
        exit();
    }
    
    try {
        // Vérifier le token
        $stmt = $pdo->prepare("
            SELECT t.*, a.id as admin_id, a.email, a.username 
            FROM password_reset_tokens t
            JOIN admins a ON t.admin_id = a.id
            WHERE t.token = ? AND t.used = 0 AND t.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        
        if (!$tokenData) {
            header('Location: forgot-password.php?error=' . urlencode('Invalid or expired reset link'));
            exit();
        }
        
        // Hasher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $tokenData['admin_id']]);
        
        // Marquer le token comme utilisé
        $stmt = $pdo->prepare("UPDATE password_reset_tokens SET used = 1 WHERE token = ?");
        $stmt->execute([$token]);
        
        // Envoyer l'email de confirmation
        sendPasswordChangedEmail(
            $tokenData['email'],
            $tokenData['username'],
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        );
        
        // Journaliser le changement
        logPasswordChange($tokenData['admin_id'], $_SERVER['REMOTE_ADDR']);
        
        // Supprimer le token de la session
        unset($_SESSION['reset_token']);
        
        // Redirection avec succès
        header('Location: login.php?success=' . urlencode('Password successfully reset! You can now login with your new password.'));
        exit();
        
    } catch (PDOException $e) {
        error_log("Password update error: " . $e->getMessage());
        header('Location: reset-password.php?token=' . $token . '&error=' . urlencode('An error occurred. Please try again.'));
        exit();
    }
} else {
    header('Location: forgot-password.php');
    exit();
}

// Fonction de journalisation
function logPasswordChange($adminId, $ipAddress) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO security_logs (admin_id, action, ip_address, user_agent) VALUES (?, 'password_changed', ?, ?)");
    $stmt->execute([$adminId, $ipAddress, $_SERVER['HTTP_USER_AGENT'] ?? '']);
}
?>