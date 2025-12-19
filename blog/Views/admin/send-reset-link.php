<?php
// send-reset-link.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Simulation d'envoi d'email
    $_SESSION['reset_email'] = $email;
    
    // Rediriger vers une page de confirmation
    header('Location: reset-sent.php');
    exit();
} else {
    header('Location: forgot-password.php');
    exit();
}
?>