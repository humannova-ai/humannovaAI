<?php
/**
 * Modèle Utilisateur
 * Gestion des utilisateurs et leurs participations
 */

class Utilisateur {
    private $conn;
    private $table = "utilisateurs";

    public $id;
    public $nom;
    public $prenom;
    public $email;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer ou récupérer un utilisateur par email
     */
    public function getOrCreate() {
        // Vérifier si l'utilisateur existe
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id = $row['id'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            return true;
        }
        
        // Créer un nouveau utilisateur
        $query = "INSERT INTO " . $this->table . " (nom, prenom, email) VALUES (:nom, :prenom, :email)";
        $stmt = $this->conn->prepare($query);
        
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenom = htmlspecialchars(strip_tags($this->prenom));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }

    /**
     * Enregistrer un résultat de quiz
     */
    public function enregistrerResultatQuiz($evenement_id, $score, $total, $reponses) {
        try {
            $this->conn->beginTransaction();
            
            $pourcentage = ($score / $total) * 100;
            
            // Insérer le résultat global
            $query = "INSERT INTO resultats_quiz (utilisateur_id, evenement_id, score, total_questions, pourcentage) 
                      VALUES (:utilisateur_id, :evenement_id, :score, :total, :pourcentage)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':utilisateur_id', $this->id);
            $stmt->bindParam(':evenement_id', $evenement_id);
            $stmt->bindParam(':score', $score);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':pourcentage', $pourcentage);
            $stmt->execute();
            
            $resultat_id = $this->conn->lastInsertId();
            
            // Insérer les détails des réponses
            $query = "INSERT INTO reponses_utilisateur (resultat_id, question_id, reponse_id, est_correcte) 
                      VALUES (:resultat_id, :question_id, :reponse_id, :est_correcte)";
            $stmt = $this->conn->prepare($query);
            
            foreach($reponses as $reponse) {
                $stmt->bindParam(':resultat_id', $resultat_id);
                $stmt->bindParam(':question_id', $reponse['question_id']);
                $stmt->bindParam(':reponse_id', $reponse['reponse_id']);
                $stmt->bindParam(':est_correcte', $reponse['est_correcte'], PDO::PARAM_BOOL);
                $stmt->execute();
            }
            
            $this->conn->commit();
            return array('success' => true, 'resultat_id' => $resultat_id);
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Enregistrer une participation à un événement normal
     */
    public function enregistrerParticipation($evenement_id, $commentaire, $fichier = null) {
        try {
            $query = "INSERT INTO participations (utilisateur_id, evenement_id, commentaire, fichier_url, statut) 
                      VALUES (:utilisateur_id, :evenement_id, :commentaire, :fichier_url, 'en_attente')";
            
            $stmt = $this->conn->prepare($query);
            
            $commentaire = htmlspecialchars(strip_tags($commentaire));
            
            $stmt->bindParam(':utilisateur_id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':evenement_id', $evenement_id, PDO::PARAM_INT);
            $stmt->bindParam(':commentaire', $commentaire);
            $stmt->bindParam(':fichier_url', $fichier);
            
            if($stmt->execute()) {
                $participation_id = $this->conn->lastInsertId();
                return array('success' => true, 'participation_id' => $participation_id);
            }
            
            // En cas d'échec, récupérer l'erreur
            $errorInfo = $stmt->errorInfo();
            return array('success' => false, 'message' => 'Erreur SQL: ' . $errorInfo[2]);
            
        } catch(PDOException $e) {
            return array('success' => false, 'message' => 'Exception: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les résultats d'un utilisateur pour un événement
     */
    public function getResultatsQuiz($evenement_id) {
        $query = "SELECT * FROM resultats_quiz 
                  WHERE utilisateur_id = :utilisateur_id AND evenement_id = :evenement_id 
                  ORDER BY date_passage DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $this->id);
        $stmt->bindParam(':evenement_id', $evenement_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Vérifier si l'utilisateur a déjà participé à un événement
     */
    public function aDejaParticipe($evenement_id, $type) {
        if($type === 'quiz') {
            $query = "SELECT COUNT(*) as count FROM resultats_quiz 
                      WHERE utilisateur_id = :utilisateur_id AND evenement_id = :evenement_id";
        } else {
            $query = "SELECT COUNT(*) as count FROM participations 
                      WHERE utilisateur_id = :utilisateur_id AND evenement_id = :evenement_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':utilisateur_id', $this->id);
        $stmt->bindParam(':evenement_id', $evenement_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>