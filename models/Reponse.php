<?php
/**
 * Modèle Reponse
 * Gestion des réponses pour les questions
 */

class Reponse {
    private $conn;
    private $table = "reponses";

    public $id;
    public $question_id;
    public $texte_reponse;
    public $est_correcte;
    public $ordre;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une réponse
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET question_id = :question_id,
                    texte_reponse = :texte_reponse,
                    est_correcte = :est_correcte,
                    ordre = :ordre";

        $stmt = $this->conn->prepare($query);

        $this->question_id = (int)$this->question_id;
        $this->texte_reponse = htmlspecialchars(strip_tags($this->texte_reponse));
        $this->est_correcte = (bool)$this->est_correcte;
        $this->ordre = (int)$this->ordre;

        $stmt->bindParam(':question_id', $this->question_id);
        $stmt->bindParam(':texte_reponse', $this->texte_reponse);
        $stmt->bindParam(':est_correcte', $this->est_correcte, PDO::PARAM_BOOL);
        $stmt->bindParam(':ordre', $this->ordre);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Récupérer les réponses d'une question
     */
    public function readByQuestion() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE question_id = :question_id 
                  ORDER BY ordre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $this->question_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Supprimer les réponses d'une question
     */
    public function deleteByQuestion() {
        $query = "DELETE FROM " . $this->table . " WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $this->question_id);
        return $stmt->execute();
    }

    /**
     * Mettre à jour une réponse
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET texte_reponse = :texte_reponse,
                    est_correcte = :est_correcte,
                    ordre = :ordre
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->texte_reponse = htmlspecialchars(strip_tags($this->texte_reponse));
        $this->est_correcte = (bool)$this->est_correcte;
        $this->ordre = (int)$this->ordre;
        $this->id = (int)$this->id;

        $stmt->bindParam(':texte_reponse', $this->texte_reponse);
        $stmt->bindParam(':est_correcte', $this->est_correcte, PDO::PARAM_BOOL);
        $stmt->bindParam(':ordre', $this->ordre);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
?>