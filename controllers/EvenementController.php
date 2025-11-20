<?php
/**
 * Contrôleur Evenement
 * Gestion de la logique métier pour les événements
 */

require_once '../config/database.php';
require_once '../models/Evenement.php';
require_once '../models/Question.php';
require_once '../models/Reponse.php';

class EvenementController {
    private $db;
    private $evenement;
    private $question;
    private $reponse;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->evenement = new Evenement($this->db);
        $this->question = new Question($this->db);
        $this->reponse = new Reponse($this->db);
    }

    /**
     * Récupérer tous les événements
     */
    public function getAllEvenements() {
        $stmt = $this->evenement->readAll();
        $evenements = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $evenements[] = $row;
        }
        
        return $evenements;
    }

    /**
     * Récupérer un événement avec ses questions et réponses
     */
    public function getEvenementComplet($id) {
        $this->evenement->id = $id;
        
        if(!$this->evenement->readOne()) {
            return null;
        }
        
        $data = array(
            'id' => $this->evenement->id,
            'type' => $this->evenement->type,
            'titre' => $this->evenement->titre,
            'description' => $this->evenement->description,
            'date_debut' => $this->evenement->date_debut,
            'date_fin' => $this->evenement->date_fin,
            'image_url' => $this->evenement->image_url,
            'nombre_questions' => $this->evenement->nombre_questions,
            'questions' => array()
        );
        
        // Si c'est un quiz, récupérer les questions et réponses
        if($this->evenement->type === 'quiz') {
            $this->question->evenement_id = $id;
            $questions_stmt = $this->question->readByEvenement();
            
            while($question_row = $questions_stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->reponse->question_id = $question_row['id'];
                $reponses_stmt = $this->reponse->readByQuestion();
                
                $reponses = array();
                while($reponse_row = $reponses_stmt->fetch(PDO::FETCH_ASSOC)) {
                    $reponses[] = $reponse_row;
                }
                
                $question_row['reponses'] = $reponses;
                $data['questions'][] = $question_row;
            }
        }
        
        return $data;
    }

    /**
     * Créer un événement
     */
    public function createEvenement($data) {
        try {
            $this->db->beginTransaction();
            
            // Créer l'événement
            $this->evenement->type = $data['type'];
            $this->evenement->titre = $data['titre'];
            $this->evenement->description = $data['description'];
            $this->evenement->date_debut = $data['date_debut'];
            $this->evenement->date_fin = $data['date_fin'];
            $this->evenement->image_url = $data['image_url'] ?? 'https://via.placeholder.com/600x400?text=No+Image';
            $this->evenement->nombre_questions = $data['nombre_questions'] ?? 0;
            
            if(!$this->evenement->create()) {
                throw new Exception("Erreur lors de la création de l'événement");
            }
            
            $evenement_id = $this->evenement->id;
            
            // Si c'est un quiz, créer les questions et réponses
            if($data['type'] === 'quiz' && isset($data['questions'])) {
                foreach($data['questions'] as $index => $question_data) {
                    $this->question->evenement_id = $evenement_id;
                    $this->question->texte_question = $question_data['texte'];
                    $this->question->ordre = $index + 1;
                    
                    if(!$this->question->create()) {
                        throw new Exception("Erreur lors de la création de la question " . ($index + 1));
                    }
                    
                    $question_id = $this->question->id;
                    
                    // Créer les réponses
                    if(isset($question_data['reponses'])) {
                        foreach($question_data['reponses'] as $rep_index => $reponse_data) {
                            $this->reponse->question_id = $question_id;
                            $this->reponse->texte_reponse = $reponse_data['texte'];
                            $this->reponse->est_correcte = isset($question_data['reponse_correcte']) && 
                                                           $question_data['reponse_correcte'] == $rep_index;
                            $this->reponse->ordre = $rep_index + 1;
                            
                            if(!$this->reponse->create()) {
                                throw new Exception("Erreur lors de la création de la réponse");
                            }
                        }
                    }
                }
            }
            
            $this->db->commit();
            return array('success' => true, 'id' => $evenement_id, 'message' => 'Événement créé avec succès');
            
        } catch(Exception $e) {
            $this->db->rollBack();
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Mettre à jour un événement
     */
    public function updateEvenement($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Mettre à jour l'événement
            $this->evenement->id = $id;
            $this->evenement->type = $data['type'];
            $this->evenement->titre = $data['titre'];
            $this->evenement->description = $data['description'];
            $this->evenement->date_debut = $data['date_debut'];
            $this->evenement->date_fin = $data['date_fin'];
            $this->evenement->image_url = $data['image_url'] ?? 'https://via.placeholder.com/600x400?text=No+Image';
            $this->evenement->nombre_questions = $data['nombre_questions'] ?? 0;
            
            if(!$this->evenement->update()) {
                throw new Exception("Erreur lors de la mise à jour de l'événement");
            }
            
            // Si c'est un quiz, supprimer les anciennes questions et créer les nouvelles
            if($data['type'] === 'quiz') {
                // Supprimer les anciennes questions (les réponses seront supprimées par CASCADE)
                $this->question->evenement_id = $id;
                $this->question->deleteByEvenement();
                
                // Créer les nouvelles questions
                if(isset($data['questions'])) {
                    foreach($data['questions'] as $index => $question_data) {
                        $this->question->evenement_id = $id;
                        $this->question->texte_question = $question_data['texte'];
                        $this->question->ordre = $index + 1;
                        
                        if(!$this->question->create()) {
                            throw new Exception("Erreur lors de la création de la question " . ($index + 1));
                        }
                        
                        $question_id = $this->question->id;
                        
                        // Créer les réponses
                        if(isset($question_data['reponses'])) {
                            foreach($question_data['reponses'] as $rep_index => $reponse_data) {
                                $this->reponse->question_id = $question_id;
                                $this->reponse->texte_reponse = $reponse_data['texte'];
                                $this->reponse->est_correcte = isset($question_data['reponse_correcte']) && 
                                                               $question_data['reponse_correcte'] == $rep_index;
                                $this->reponse->ordre = $rep_index + 1;
                                
                                if(!$this->reponse->create()) {
                                    throw new Exception("Erreur lors de la création de la réponse");
                                }
                            }
                        }
                    }
                }
            }
            
            $this->db->commit();
            return array('success' => true, 'message' => 'Événement mis à jour avec succès');
            
        } catch(Exception $e) {
            $this->db->rollBack();
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Supprimer un événement
     */
    public function deleteEvenement($id) {
        $this->evenement->id = $id;
        
        if($this->evenement->delete()) {
            return array('success' => true, 'message' => 'Événement supprimé avec succès');
        }
        
        return array('success' => false, 'message' => 'Erreur lors de la suppression');
    }
}

// Gestion des requêtes AJAX
if($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new EvenementController();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    header('Content-Type: application/json');
    
    switch($action) {
        case 'getAll':
            echo json_encode($controller->getAllEvenements());
            break;
            
        case 'getOne':
            $id = $_GET['id'] ?? 0;
            echo json_encode($controller->getEvenementComplet($id));
            break;
            
        case 'create':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->createEvenement($data));
            break;
            
        case 'update':
            $id = $_POST['id'] ?? $_GET['id'] ?? 0;
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->updateEvenement($id, $data));
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? $_GET['id'] ?? 0;
            echo json_encode($controller->deleteEvenement($id));
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Action non reconnue'));
    }
}
?>