<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session
session_start();

// Inclure l'autoloader s'il existe
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../config/database.php';
// Include AuthController only if present (some deployments remove auth)
$authAvailable = false;
$authPath = __DIR__ . '/../controller/AuthController.php';
if (file_exists($authPath)) {
    require_once $authPath;
    $authAvailable = class_exists('AuthController');
}

// Ensure a minimal AuthController exists so static analyzers and runtime checks don't fail.
// This stub will only be defined if no real controller is present; $authAvailable remains accurate.
if (!class_exists('AuthController')) {
    class AuthController {
        public function register(array $data) {
            return ['success' => false, 'message' => 'Authentication controller unavailable on this installation'];
        }
        public function login($email, $mdp) {
            return ['success' => false, 'message' => 'Authentication controller unavailable on this installation'];
        }
        public function logout() {
            return ['success' => false, 'message' => 'Authentication controller unavailable on this installation'];
        }
    }
}

// Gérer les requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    if ($authAvailable) {
        $authController = new AuthController();
    } else {
        echo json_encode(['success' => false, 'message' => 'Authentication controller unavailable on this installation']);
        exit();
    }
    
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

// Redirect to the login page (home removed)
header('Location: login.php');
exit();
