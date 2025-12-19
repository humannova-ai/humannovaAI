<?php
// Controllers/ReactionController.php
require_once __DIR__ . '/../Models/Reaction.php';

class ReactionController {
    private $reactionModel;
    
    public function __construct() {
        $this->reactionModel = new Reaction();
    }
    
    public function handle() {
        // Démarrer la session si pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Headers pour JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        // Gérer les pré-requêtes CORS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Journalisation détaillée
        error_log("=== ReactionController ===");
        error_log("Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("Query: " . $_SERVER['QUERY_STRING']);
        error_log("POST data: " . print_r($_POST, true));
        
        try {
            switch($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $this->getReactions();
                    break;
                    
                case 'POST':
                    $this->addReaction();
                    break;
                    
                case 'DELETE':
                    $this->removeReaction();
                    break;
                    
                default:
                    $this->jsonResponse(['success' => false, 'error' => 'Méthode non autorisée'], 405);
                    break;
            }
        } catch (Exception $e) {
            error_log("ReactionController error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(['success' => false, 'error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }
    
    private function getReactions() {
        $article_id = $_GET['article_id'] ?? null;
        
        error_log("GET Reactions - Article ID: " . $article_id);
        
        if (!$article_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID requis'], 400);
            return;
        }
        
        $article_id = (int)$article_id;
        if ($article_id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID invalide'], 400);
            return;
        }
        
        // Utiliser l'ID de session ou générer un ID unique pour les invités
        $user_id = $_SESSION['user_id'] ?? $this->getGuestId();
        error_log("User ID: " . $user_id);
        
        try {
            $reactions = $this->reactionModel->getByArticle($article_id);
            $user_reaction = $this->reactionModel->getUserReaction($article_id, $user_id);
            $available_emojis = $this->reactionModel->getAvailableEmojis();
            $stats = $this->reactionModel->getStats($article_id);
            
            error_log("Reactions found: " . count($reactions));
            error_log("User reaction: " . ($user_reaction ?? 'none'));
            
            $this->jsonResponse([
                'success' => true,
                'reactions' => $reactions,
                'user_reaction' => $user_reaction,
                'available_emojis' => $available_emojis,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            error_log("Error getting reactions: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la récupération: ' . $e->getMessage()], 500);
        }
    }
    
    private function addReaction() {
        // Vérifier si c'est une requête POST multipart/form-data
        $article_id = $_POST['article_id'] ?? null;
        $emoji = $_POST['emoji'] ?? null;
        
        // Si pas dans $_POST, essayer de lire php://input
        if (!$article_id || !$emoji) {
            $input = file_get_contents('php://input');
            error_log("Raw input: " . $input);
            parse_str($input, $data);
            
            $article_id = $data['article_id'] ?? null;
            $emoji = $data['emoji'] ?? null;
        }
        
        error_log("ADD Reaction - Article ID: " . $article_id . ", Emoji: " . $emoji);
        
        if (!$article_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID requis'], 400);
            return;
        }
        
        if (!$emoji) {
            $this->jsonResponse(['success' => false, 'error' => 'Émoji requis'], 400);
            return;
        }
        
        $article_id = (int)$article_id;
        if ($article_id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID invalide'], 400);
            return;
        }
        
        $user_id = $_SESSION['user_id'] ?? $this->getGuestId();
        error_log("Adding reaction for user: " . $user_id);
        
        try {
            $success = $this->reactionModel->add($article_id, $user_id, $emoji);
            
            error_log("Reaction add result: " . ($success ? 'SUCCESS' : 'FAILED'));
            
            if ($success) {
                $reactions = $this->reactionModel->getByArticle($article_id);
                $stats = $this->reactionModel->getStats($article_id);
                
                $this->jsonResponse([
                    'success' => true,
                    'message' => 'Réaction ajoutée avec succès',
                    'reactions' => $reactions,
                    'user_reaction' => $emoji,
                    'stats' => $stats
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Émoji invalide ou erreur base de données'], 400);
            }
        } catch (Exception $e) {
            error_log("Error adding reaction: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()], 500);
        }
    }
    
    private function removeReaction() {
        $input = file_get_contents('php://input');
        parse_str($input, $data);
        
        $article_id = $data['article_id'] ?? null;
        
        error_log("REMOVE Reaction - Article ID: " . $article_id);
        
        if (!$article_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID requis'], 400);
            return;
        }
        
        $article_id = (int)$article_id;
        if ($article_id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Article ID invalide'], 400);
            return;
        }
        
        $user_id = $_SESSION['user_id'] ?? $this->getGuestId();
        
        try {
            $success = $this->reactionModel->remove($article_id, $user_id);
            
            $this->jsonResponse([
                'success' => $success,
                'message' => $success ? 'Réaction supprimée' : 'Erreur lors de la suppression',
                'reactions' => $success ? $this->reactionModel->getByArticle($article_id) : []
            ]);
        } catch (Exception $e) {
            error_log("Error removing reaction: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Erreur lors de la suppression: ' . $e->getMessage()], 500);
        }
    }
    
    private function getGuestId() {
        if (!isset($_SESSION['guest_id'])) {
            $_SESSION['guest_id'] = 'guest_' . uniqid() . '_' . md5($_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
        }
        return $_SESSION['guest_id'];
    }
    
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>