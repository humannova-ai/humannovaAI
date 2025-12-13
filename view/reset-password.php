<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Utilisateur.php';

$database = new Database();
$db = $database->getConnection();
$utilisateur = new Utilisateur($db);

$error = '';
$success = '';

// Vérifier si le token est valide
$token = $_GET['token'] ?? '';
$isValidToken = false;

if (!empty($token) && 
    isset($_SESSION['reset_token']) && 
    isset($_SESSION['reset_email']) && 
    isset($_SESSION['reset_expires']) &&
    $token === $_SESSION['reset_token'] &&
    time() < strtotime($_SESSION['reset_expires'])) {
    
    $isValidToken = true;
    $email = $_SESSION['reset_email'];
}

// Traitement du formulaire de réinitialisation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
    if (!$isValidToken) {
        $error = "Le lien de réinitialisation est invalide ou a expiré.";
    } else {
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        } elseif (strlen($password) < 8) {
            $error = "Le mot de passe doit contenir au moins 8 caractères.";
        } else {
            if ($utilisateur->updatePassword($email, $password)) {
                // Supprimer les variables de session après utilisation
                unset($_SESSION['reset_token']);
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_expires']);
                
                // Redirection immédiate vers la page de connexion
                $_SESSION['success_message'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                header('Location: login.php');
                exit();
            } else {
                $error = "Une erreur est survenue lors de la réinitialisation du mot de passe. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe - PRISM FLUX</title>
    <link rel="stylesheet" href="../public/assets/css/templatemo-prism-flux.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            padding: 2rem;
        }
        
        .auth-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .auth-title {
            color: #fff;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            color: #fff;
            font-size: 1rem;
        }
        
        .btn {
            display: inline-block;
            background: #4e54c8;
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s;
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
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .strength-weak {
            color: #ff6b6b;
        }
        
        .strength-medium {
            color: #f1c40f;
        }
        
        .strength-strong {
            color: #51cf66;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Réinitialisation du mot de passe</h1>
            
            <?php if ($error): ?>
                <div class="text-danger mb-3"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="text-success mb-3">
                    <?php 
                    echo htmlspecialchars($_SESSION['success_message']);
                    unset($_SESSION['success_message']); // Supprimer le message après affichage
                    ?>
                </div>
            <?php elseif ($isValidToken): ?>
                <p class="text-center" style="color: #fff; margin-bottom: 1.5rem;">
                    Veuillez entrer votre nouveau mot de passe.
                </p>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Nouveau mot de passe" required minlength="8">
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirmer le mot de passe" required minlength="8">
                        <div id="password-match" class="password-strength"></div>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="btn" disabled>Réinitialiser le mot de passe</button>
                </form>
                
                 
                
                <script>
                    const passwordInput = document.getElementById('password');
                    const confirmPasswordInput = document.getElementById('confirm_password');
                    const passwordStrength = document.getElementById('password-strength');
                    const passwordMatch = document.getElementById('password-match');
                    const submitBtn = document.getElementById('submitBtn');
                    
                    function checkPasswordStrength(password) {
                        // Vérifier la longueur
                        if (password.length === 0) {
                            return { strength: 0, message: '' };
                        } else if (password.length < 8) {
                            return { strength: 1, message: 'Le mot de passe doit contenir au moins 8 caractères', class: 'strength-weak' };
                        }
                        
                        // Vérifier la complexité
                        let strength = 0;
                        
                        if (/[a-z]/.test(password)) strength++;
                        if (/[A-Z]/.test(password)) strength++;
                        if (/[0-9]/.test(password)) strength++;
                        if (/[^a-zA-Z0-9]/.test(password)) strength++;
                        
                        if (strength <= 2) {
                            return { strength, message: 'Mot de passe faible', class: 'strength-weak' };
                        } else if (strength <= 3) {
                            return { strength, message: 'Mot de passe moyen', class: 'strength-medium' };
                        } else {
                            return { strength, message: 'Mot de passe fort', class: 'strength-strong' };
                        }
                    }
                    
                    function checkPasswordMatch() {
                        const password = passwordInput.value;
                        const confirmPassword = confirmPasswordInput.value;
                        
                        if (confirmPassword.length === 0) {
                            passwordMatch.textContent = '';
                            return false;
                        }
                        
                        if (password === confirmPassword) {
                            passwordMatch.textContent = 'Les mots de passe correspondent';
                            passwordMatch.className = 'password-strength strength-strong';
                            return true;
                        } else {
                            passwordMatch.textContent = 'Les mots de passe ne correspondent pas';
                            passwordMatch.className = 'password-strength strength-weak';
                            return false;
                        }
                    }
                    
                    function updateSubmitButton() {
                        const password = passwordInput.value;
                        const confirmPassword = confirmPasswordInput.value;
                        
                        // Activer le bouton si le mot de passe fait au moins 8 caractères et que les mots de passe correspondent
                        submitBtn.disabled = !(password.length >= 8 && password === confirmPassword);
                    }
                    
                    passwordInput.addEventListener('input', function() {
                        const result = checkPasswordStrength(this.value);
                        passwordStrength.textContent = result.message || '';
                        passwordStrength.className = 'password-strength ' + (result.class || '');
                        updateSubmitButton();
                    });
                    
                    confirmPasswordInput.addEventListener('input', function() {
                        checkPasswordMatch();
                        updateSubmitButton();
                    });
                </script>
            <?php else: ?>
                <div class="text-danger mb-3">Le lien de réinitialisation est invalide ou a expiré.</div>
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="login-link">Demander un nouveau lien</a>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-3">
                <a href="login.php" class="login-link">Retour à la page de connexion</a>
            </div>
        </div>
    </div>
</body>
</html>
