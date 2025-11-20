<?php
/**
 * Modèle Question
 * Gestion des questions pour les événements de type quiz
 */

class Question {
    private $conn;
    private $table = "questions";

    public $id;
    public $evenement_id;
    public $texte_question;
    public $ordre;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer une question
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET evenement_id = :evenement_id,
                    texte_question = :texte_question,
                    ordre = :ordre";

        $stmt = $this->conn->prepare($query);

        $this->evenement_id = (int)$this->evenement_id;
        $this->texte_question = htmlspecialchars(strip_tags($this->texte_question));
        $this->ordre = (int)$this->ordre;

        $stmt->bindParam(':evenement_id', $this->evenement_id);
        $stmt->bindParam(':texte_question', $this->texte_question);
        $stmt->bindParam(':ordre', $this->ordre);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Récupérer les questions d'un événement
     */
    public function readByEvenement() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE evenement_id = :evenement_id 
                  ORDER BY ordre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':evenement_id', $this->evenement_id);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Supprimer les questions d'un événement
     */
    public function deleteByEvenement() {
        $query = "DELETE FROM " . $this->table . " WHERE evenement_id = :evenement_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':evenement_id', $this->evenement_id);
        return $stmt->execute();
    }

    /**
     * Mettre à jour une question
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET texte_question = :texte_question,
                    ordre = :ordre
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->texte_question = htmlspecialchars(strip_tags($this->texte_question));
        $this->ordre = (int)$this->ordre;
        $this->id = (int)$this->id;

        $stmt->bindParam(':texte_question', $this->texte_question);
        $stmt->bindParam(':ordre', $this->ordre);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}
?>