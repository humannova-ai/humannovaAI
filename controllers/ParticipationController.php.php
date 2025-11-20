<?php
/**
 * Contrôleur Participation
 * Gestion des participations et résultats
 */

require_once '../config/database.php';
require_once '../models/Utilisateur.php';
require_once '../models/Evenement.php';

class ParticipationController {
    private $db;
    private $utilisateur;
    private $evenement;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->utilisateur = new Utilisateur($this->db);
        $this->evenement = new Evenement($this->db);
    }

    /**
     * Soumettre un quiz
     */
    public function soumettreQuiz($data) {
        try {
            // Créer ou récupérer l'utilisateur
            $this->utilisateur->nom = $data['nom'];
            $this->utilisateur->prenom = $data['prenom'];
            $this->utilisateur->email = $data['email'];
            
            if(!$this->utilisateur->getOrCreate()) {
                return array('success' => false, 'message' => 'Erreur utilisateur');
            }
            
            // Récupérer l'événement et ses questions
            $this->evenement->id = $data['evenement_id'];
            $stmt = $this->evenement->readWithQuestions();
            
            $questions = array();
            $current_question = null;
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if(!isset($questions[$row['question_id']])) {
                    $questions[$row['question_id']] = array(
                        'id' => $row['question_id'],
                        'reponses' => array()
                    );
                }
                
                if($row['reponse_id']) {
                    $questions[$row['question_id']]['reponses'][$row['reponse_id']] = array(
                        'id' => $row['reponse_id'],
                        'est_correcte' => $row['est_correcte']
                    );
                }
            }
            
            // Calculer le score
            $score = 0;
            $total = count($questions);
            $reponses_details = array();
            
            foreach($data['reponses'] as $question_id => $reponse_id) {
                $est_correcte = false;
                
                if(isset($questions[$question_id]['reponses'][$reponse_id])) {
                    $est_correcte = (bool)$questions[$question_id]['reponses'][$reponse_id]['est_correcte'];
                    if($est_correcte) {
                        $score++;
                    }
                }
                
                $reponses_details[] = array(
                    'question_id' => $question_id,
                    'reponse_id' => $reponse_id,
                    'est_correcte' => $est_correcte
                );
            }
            
            // Enregistrer le résultat
            $result = $this->utilisateur->enregistrerResultatQuiz(
                $data['evenement_id'],
                $score,
                $total,
                $reponses_details
            );
            
            if($result['success']) {
                return array(
                    'success' => true,
                    'score' => $score,
                    'total' => $total,
                    'pourcentage' => round(($score / $total) * 100, 2),
                    'resultat_id' => $result['resultat_id']
                );
            }
            
            return $result;
            
        } catch(Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Soumettre une participation à un événement normal
     */
    public function soumettreParticipation($data, $file = null) {
        try {
            // Créer ou récupérer l'utilisateur
            $this->utilisateur->nom = $data['nom'];
            $this->utilisateur->prenom = $data['prenom'];
            $this->utilisateur->email = $data['email'];
            
            if(!$this->utilisateur->getOrCreate()) {
                return array('success' => false, 'message' => 'Erreur utilisateur');
            }
            
            // Gérer l'upload du fichier si présent
            $fichier_url = null;
            if($file && $file['error'] === UPLOAD_ERR_OK) {
                $fichier_url = $this->uploadFichier($file);
                if(!$fichier_url) {
                    return array('success' => false, 'message' => 'Erreur lors de l\'upload du fichier');
                }
            }
            
            // Enregistrer la participation
            $result = $this->utilisateur->enregistrerParticipation(
                $data['evenement_id'],
                $data['commentaire'],
                $fichier_url
            );
            
            return $result;
            
        } catch(Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Upload d'un fichier
     */
    private function uploadFichier($file) {
        $upload_dir = '../uploads/';
        
        // Créer le dossier s'il n'existe pas
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Générer un nom unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nom_fichier = uniqid() . '_' . time() . '.' . $extension;
        $chemin_complet = $upload_dir . $nom_fichier;
        
        // Vérifier le type de fichier (sécurité)
        $types_autorises = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip');
        if(!in_array(strtolower($extension), $types_autorises)) {
            return false;
        }
        
        // Vérifier la taille (max 5MB)
        if($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        
        // Déplacer le fichier
        if(move_uploaded_file($file['tmp_name'], $chemin_complet)) {
            return $nom_fichier;
        }
        
        return false;
    }

    /**
     * Obtenir les statistiques d'un utilisateur
     */
    public function getStatistiquesUtilisateur($email) {
        $this->utilisateur->email = $email;
        if(!$this->utilisateur->getOrCreate()) {
            return null;
        }
        
        // Résultats quiz
        $query = "SELECT e.titre, rq.score, rq.total_questions, rq.pourcentage, rq.date_passage 
                  FROM resultats_quiz rq
                  JOIN evenements e ON rq.evenement_id = e.id
                  WHERE rq.utilisateur_id = :utilisateur_id
                  ORDER BY rq.date_passage DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur->id);
        $stmt->execute();
        
        $quiz_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Participations
        $query = "SELECT e.titre, p.commentaire, p.fichier_url, p.statut, p.date_participation 
                  FROM participations p
                  JOIN evenements e ON p.evenement_id = e.id
                  WHERE p.utilisateur_id = :utilisateur_id
                  ORDER BY p.date_participation DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $this->utilisateur->id);
        $stmt->execute();
        
        $participations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array(
            'utilisateur' => array(
                'nom' => $this->utilisateur->nom,
                'prenom' => $this->utilisateur->prenom,
                'email' => $this->utilisateur->email
            ),
            'quiz_results' => $quiz_results,
            'participations' => $participations
        );
    }
}

// Gestion des requêtes AJAX
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ParticipationController();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    header('Content-Type: application/json');
    
    switch($action) {
        case 'soumettreQuiz':
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($controller->soumettreQuiz($data));
            break;
            
        case 'soumettreParticipation':
            $data = $_POST;
            $file = isset($_FILES['fichier']) ? $_FILES['fichier'] : null;
            echo json_encode($controller->soumettreParticipation($data, $file));
            break;
            
        case 'getStatistiques':
            $email = $_POST['email'] ?? '';
            echo json_encode($controller->getStatistiquesUtilisateur($email));
            break;
            
        default:
            echo json_encode(array('success' => false, 'message' => 'Action non reconnue'));
    }
}
?>