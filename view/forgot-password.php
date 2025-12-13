<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Configuration de l'API SendGrid
$emailConfig = require_once __DIR__ . '/../config/email.php';
$apiKey = $emailConfig['sendgrid_api_key'];
$url = $emailConfig['sendgrid_api_url'];

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Utilisateur.php';

$database = new Database();
$db = $database->getConnection();
$utilisateur = new Utilisateur($db);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    if ($utilisateur->emailExists($email)) {
        // Générer un token unique
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Stocker le token dans la session
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_expires'] = $expires;
        
        // Construire le lien de réinitialisation
        $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . '/utilisateur/view/reset-password.php?token=' . $token;
        
        // Contenu HTML de l'email
        $emailContent = '
        <div style="font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <div style="text-align: center; margin-bottom: 25px;">
                <h1 style="color: #2c3e50; margin: 0; font-size: 24px; font-weight: 600;">Réinitialisation de votre mot de passe</h1>
                <div style="height: 3px; width: 60px; background: #4e54c8; margin: 15px auto 0; border-radius: 3px;"></div>
            </div>
            
            <p style="color: #2c3e50; font-size: 15px; line-height: 1.6; margin-bottom: 25px;">
                Bonjour,<br><br>
                Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Pour procéder, veuillez cliquer sur le bouton ci-dessous :
            </p>
            
            <div style="text-align: center; margin: 35px 0;">
                <a href="' . $resetLink . '" style="background-color: #4e54c8; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: 500; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    Réinitialiser mon mot de passe
                </a>
            </div>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; margin: 25px 0; border-left: 4px solid #4e54c8;">
                <p style="color: #495057; font-size: 13px; margin: 0;">
                    <strong>Note :</strong> Ce lien de réinitialisation expirera dans <strong>1 heure</strong>.
                    Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email ou nous contacter si vous pensez qu\'il s\'agit d\'une erreur.
                </p>
            </div>
            
            <p style="color: #6c757d; font-size: 13px; line-height: 1.6; margin-bottom: 5px;">
                <strong>Besoin d\'aide ?</strong><br>
                Contactez notre équipe de support à <a href="mailto:support@prismflux.com" style="color: #4e54c8; text-decoration: none;">support@prismflux.com</a> pour toute assistance.
            </p>
            
            <hr style="border: none; border-top: 1px solid #e9ecef; margin: 25px 0 20px;">
            
            <div style="text-align: center; color: #6c757d; font-size: 12px;">
                <p style="margin: 5px 0;">© ' . date('Y') . ' PRISM FLUX. Tous droits réservés.</p>
                <p style="margin: 5px 0; color: #adb5bd;">
                    Cet email a été envoyé à ' . $email . '. Si vous ne reconnaissez pas cette adresse, veuillez ignorer cet email.
                </p>
            </div>
        </div>';

        // Préparation des données pour l'API SendGrid
        $data = [
            'personalizations' => [
                [
                    'to' => [['email' => $email]],
                    'subject' => "Réinitialisation de votre mot de passe"
                ]
            ],
            'from' => ['email' => 'promanageai@gmail.com', 'name' => 'PRISM FLUX'],
            'content' => [
                ['type' => 'text/plain', 'value' => strip_tags(str_replace(['<br>', '<p>', '</p>'], ["\n", "\n", "\n"], $emailContent))],
                ['type' => 'text/html', 'value' => $emailContent]
            ]
        ];

        // Envoi de la requête à l'API SendGrid
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $success = 'Un email de réinitialisation a été envoyé à ' . $email;
        } else {
            $error = 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer plus tard.';
            error_log("Erreur d'envoi d'email - Code: $httpCode - Erreur: $curlError - Réponse: $response");
        }
    } else {
        $error = "Aucun compte n'est associé à cette adresse email.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - PRISM FLUX</title>
    <link rel="stylesheet" href="../public/assets/css/templatemo-prism-flux.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .auth-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
        
        .auth-title {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4e54c8;
            box-shadow: 0 0 0 2px rgba(78, 84, 200, 0.2);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .btn {
            background: #4e54c8;
            color: white;
            border: none;
            padding: 12px 20px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn:hover {
            background: #434190;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .text-danger {
            color: #ff6b6b;
        }
        
        .text-success {
            color: #51cf66;
        }
        
        .login-link {
            color: #4e54c8;
            text-decoration: none;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Mot de passe oublié</h1>
            
            <?php if ($error): ?>
                <div class="text-danger mb-3"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="text-success mb-3"><?php echo htmlspecialchars($success); ?></div>
                <div class="text-center mt-3">
                    <a href="login.php" class="login-link">Retour à la page de connexion</a>
                </div>
            <?php else: ?>
                <p class="text-center" style="color: #fff; margin-bottom: 1.5rem;">
                    Entrez votre adresse email pour recevoir un lien de réinitialisation.
                </p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Votre adresse email" required>
                    </div>
                    
                    <button type="submit" class="btn">Envoyer le lien de réinitialisation</button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="login.php" class="login-link">Retour à la page de connexion</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
