<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session
session_start();

// Inclure l'autoloader s'il existe
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Inclure les fichiers nécessaires
// Authentication removed — auth controllers/models will be deleted.
// Keep database config only if still needed elsewhere.
if (file_exists(__DIR__ . '/config/database.php')) {
    require_once __DIR__ . '/config/database.php';
}

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
    
    // Rediriger vers la page d'accueil du site
    header('Location: view/index.html?logout=1');
    exit();
}

// No auth handling: redirect to public index page
header('Location: view/index.html');
exit();
