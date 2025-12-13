<?php
// Démarrer la session
session_start();

// Gérer la déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Détruire toutes les données de session
    $_SESSION = array();

    // Détruire le cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], 
            $params["domain"],
            $params["secure"], 
            $params["httponly"]
        );
    }
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page de connexion avec un message
    header('Location: login.php?logout=1');
    exit();
}

// Vérifier si l'utilisateur vient de se déconnecter
$logoutMessage = '';
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $logoutMessage = 'Vous avez été déconnecté avec succès.';
}
?>
<style>
#chatButton {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 60px;
    height: 60px;
    background: #00DCB9;
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 26px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 99999;
    transition: 0.2s ease;
}
#chatButton:hover {
    transform: scale(1.1);
    background: #00DCB9;
}

#chatWindow {
    position: fixed;
    bottom: 95px;
    right: 25px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    display: none;
    flex-direction: column;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    z-index: 100000;
    overflow: hidden;
    opacity: 0;
    transform: scale(0.8);
    transition: opacity 0.25s ease, transform 0.25s ease;
}
#chatWindow.open {
    display: flex;
    opacity: 1;
    transform: scale(1);
}

#chatHeader {
    background: #00DCB9;
    color: white;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
#chatHeader button {
    background: none;
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
}
iframe {
    flex: 1;
    width: 100%;
    border: none;
}
</style>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Manage AI - Connexion</title>
    <link rel="stylesheet" href="../public/assets/css/templatemo-prism-flux.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .logo-large {
            margin-bottom: 2rem;
        }
        
        .logo-large .logo-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }
        
        .logo-large .logo-text {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(90deg, #6e45e2, #88d3ce);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
            margin-bottom: 1rem;
        }
        
        .welcome-text {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            font-weight: 300;
        }
        
        .auth-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .auth-tab {
            padding: 1rem 2rem;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            position: relative;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .auth-tab.active {
            color: #fff;
        }
        
        .auth-tab.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 50%;
            height: 3px;
            background: linear-gradient(90deg, #6e45e2, #88d3ce);
            border-radius: 3px 3px 0 0;
        }
        
        .auth-form {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #6e45e2;
            box-shadow: 0 0 0 2px rgba(110, 69, 226, 0.2);
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(90deg, #6e45e2, #88d3ce);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .auth-footer {
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        .auth-footer a {
            color: #6e45e2;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .auth-footer a:hover {
            color: #88d3ce;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .auth-box {
                padding: 1.5rem;
            }
            
            .logo-large .logo-text {
                font-size: 2rem;
            }
            
            .welcome-text {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="loader-content">
            <div class="loader-prism">
                <div class="prism-face"></div>
                <div class="prism-face"></div>
                <div class="prism-face"></div>
            </div>
            <div style="color: var(--accent-purple); font-size: 18px; text-transform: uppercase; letter-spacing: 3px;">Chargement...</div>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-box">
            <!-- Logo personnalisé -->
            <div class="text-center mb-4">
                <img src="../public/images/logo2.png" alt="Logo Pro Manage AI" style="max-width: 200px; height: auto;">
            </div>
            
            <div class="logo-large" style="display: none;">
                <div class="logo-icon">
                    <div class="logo-prism">
                        <div class="prism-shape"></div>
                    </div>
                </div>
                <span class="logo-text">
                    <span class="prism">PRO MANAGE</span>
                    <span class="flux">AI</span>
                </span>
            </div>
            
            <div class="welcome-text">
                Bienvenue sur votre plateforme de gestion intelligente
            </div>
            
            <div class="auth-tabs">
                <div class="auth-tab active" data-tab="login">Connexion</div>
                <div class="auth-tab" data-tab="signup">Inscription</div>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" class="auth-form active" onsubmit="return loginUser(event)">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" class="form-control" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Mot de passe</label>
                    <input type="password" id="loginPassword" name="mdp" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group" style="text-align: right;">
                    <a href="forgot-password.php" style="color: #6e45e2; font-size: 0.9rem;">Mot de passe oublié ?</a>
                </div>
                <button type="submit" id="loginBtn" class="btn">Se connecter</button>
                <div class="auth-footer">
                    Pas encore de compte ? <a href="#" class="switch-tab" data-tab="signup">S'inscrire</a>
                </div>
                <div id="loginMessage" class="message" style="margin-top: 15px; display: none;"></div>
            </form>
            
            <!-- Signup Form -->
            <form id="signupForm" class="auth-form" onsubmit="return registerUser(event)">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" placeholder="Votre prénom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" placeholder="Votre adresse" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="Votre numéro de téléphone" required>
                </div>
                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="confirmer_mdp">Confirmer le mot de passe</label>
                    <input type="password" id="confirmer_mdp" name="confirmer_mdp" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
                <div class="auth-footer">
                    Déjà inscrit ? <a href="#" class="switch-tab" data-tab="login">Se connecter</a>
                </div>
                <div id="registerMessage" class="message" style="margin-top: 15px; display: none;"></div>
            </form>
        </div>
    </div>
    <div id="chatButton">💬</div>

<div id="chatWindow">
    <div id="chatHeader">
        Chatbot
        <button id="closeChat">×</button>
    </div>
    <iframe id="chatIframe"></iframe>
</div>
<script>
const chatButton = document.getElementById("chatButton");
const chatWindow = document.getElementById("chatWindow");
const chatIframe = document.getElementById("chatIframe");
const closeChat = document.getElementById("closeChat");

const chatbotUrl = "https://www.chatbase.co/chatbot-iframe/WAO818oBk6Ity1yhCsPT8";
let iframeLoaded = false;

chatButton.addEventListener("click", () => {
    chatWindow.style.display = "flex";
    if (!iframeLoaded) {
        chatIframe.src = chatbotUrl;
        iframeLoaded = true;
    }
    setTimeout(() => {
        chatWindow.classList.add("open");
    }, 10);
});

closeChat.addEventListener("click", () => {
    chatWindow.classList.remove("open");
    setTimeout(() => {
        chatWindow.style.display = "none";
    }, 250);
});
</script>

    <style>
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
    <script>
        // Afficher le message de déconnexion si présent
        <?php if (!empty($logoutMessage)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            alert('<?php echo addslashes($logoutMessage); ?>');
        });
        <?php endif; ?>

        // Fonction pour afficher les messages
        function showMessage(elementId, message, isError = false) {
            const messageDiv = document.getElementById(elementId);
            messageDiv.textContent = message;
            messageDiv.className = isError ? 'message error' : 'message success';
            messageDiv.style.display = 'block';
            
            // Masquer le message après 5 secondes
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        // Fonction pour gérer l'inscription
        function registerUser(event) {
            event.preventDefault();
            
            // Afficher les données du formulaire dans la console
            const formData = new FormData(event.target);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            console.log("Données du formulaire:", data);
            
            // Désactiver le bouton pendant la requête
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Inscription en cours...';
            
            // Afficher un message de chargement
            showMessage('registerMessage', 'Traitement en cours...', false);

            fetch('index.php?action=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log("Réponse du serveur:", response);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP! statut: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Données de la réponse:", data);
                if (data.success) {
                    showMessage('registerMessage', data.message, false);
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else {
                    showMessage('registerMessage', data.message || 'Une erreur est survenue', true);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('registerMessage', 'Erreur de connexion au serveur: ' + error.message, true);
            })
            .finally(() => {
                // Réactiver le bouton
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });

            return false;
        }

        // Fonction pour gérer la connexion
        function loginUser(event) {
            event.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const mdp = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');
            
            // Désactiver le bouton pendant la requête
            loginBtn.disabled = true;
            loginBtn.innerHTML = 'Connexion en cours...';

            fetch('index.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, mdp })
            })
            .then(response => {
                // Si la réponse est une redirection (statut 3xx), suivre la redirection
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // Si on a déjà été redirigé
                
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    showMessage('loginMessage', data.message || 'Une erreur est survenue', true);
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = 'Se connecter';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('loginMessage', 'Une erreur est survenue lors de la connexion', true);
                loginBtn.disabled = false;
                loginBtn.innerHTML = 'Se connecter';
            });

            return false;
        }

        // Gestion des onglets de connexion/inscription
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');
            const switchTabs = document.querySelectorAll('.switch-tab');
            
            // Fonction pour changer d'onglet
            function switchTab(tabName) {
                // Mise à jour des onglets
                tabs.forEach(tab => {
                    if (tab.dataset.tab === tabName) {
                        tab.classList.add('active');
                    } else {
                        tab.classList.remove('active');
                    }
                });
                
                // Affichage du formulaire correspondant
                forms.forEach(form => {
                    if (form.id === tabName + 'Form') {
                        form.classList.add('active');
                    } else {
                        form.classList.remove('active');
                    }
                });
            }
            
            // Gestion du clic sur les onglets
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    switchTab(tabName);
                });
            });
            
            // Gestion du clic sur les liens de bas de formulaire
            switchTabs.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabName = this.dataset.tab;
                    switchTab(tabName);
                });
            });
            
            // Les gestionnaires de formulaire sont déjà gérés par les attributs onsubmit
            // dans les balises form
            
            // Masquer le loader une fois la page chargée
            window.addEventListener('load', function() {
                setTimeout(function() {
                    document.getElementById('loader').style.opacity = '0';
                    setTimeout(function() {
                        document.getElementById('loader').style.display = 'none';
                    }, 500);
                }, 1000);
            });
        });
    </script>
    
    <script src="../public/assets/js/templatemo-prism-scripts.js"></script>
</body>
</html>
