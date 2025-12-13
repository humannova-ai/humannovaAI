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
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controller/AuthController.php';

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
    header('Location: view/login.php?logout=1');
    exit();
}

// Gérer les requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    $authController = new AuthController();
    
    $action = $_GET['action'];
    $response = [];
    
    try {
        if ($action === 'register') {
            // Récupérer les données JSON du corps de la requête
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Données JSON invalides');
            }
            
            $response = $authController->register($data);
        } elseif ($action === 'login') {
            // Récupérer les données JSON du corps de la requête
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Données JSON invalides');
            }
            
            if (!isset($data['email']) || !isset($data['mdp'])) {
                throw new Exception('Email et mot de passe requis');
            }
            
            $response = $authController->login($data['email'], $data['mdp']);
        } elseif ($action === 'logout') {
            $response = $authController->logout();
        } else {
            $response = ['success' => false, 'message' => 'Action non reconnue'];
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
    
    echo json_encode($response);
    exit();
}

// Rediriger vers la page de connexion par défaut
header('Location: view/login.php');
exit();
