<?php
/**
 * Modèle Evenement
 * Gestion des opérations CRUD pour les événements
 */

class Evenement {
    private $conn;
    private $table = "evenements";

    // Propriétés
    public $id;
    public $type;
    public $titre;
    public $description;
    public $date_debut;
    public $date_fin;
    public $image_url;
    public $nombre_questions;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Récupérer tous les événements
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY date_debut DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer un événement par ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->type = $row['type'];
            $this->titre = $row['titre'];
            $this->description = $row['description'];
            $this->date_debut = $row['date_debut'];
            $this->date_fin = $row['date_fin'];
            $this->image_url = $row['image_url'];
            $this->nombre_questions = $row['nombre_questions'];
            return true;
        }
        
        return false;
    }

    /**
     * Créer un événement
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET type = :type,
                    titre = :titre,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    image_url = :image_url,
                    nombre_questions = :nombre_questions";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->date_debut = htmlspecialchars(strip_tags($this->date_debut));
        $this->date_fin = htmlspecialchars(strip_tags($this->date_fin));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->nombre_questions = (int)$this->nombre_questions;

        // Liaison des paramètres
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_debut', $this->date_debut);
        $stmt->bindParam(':date_fin', $this->date_fin);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':nombre_questions', $this->nombre_questions);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour un événement
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET type = :type,
                    titre = :titre,
                    description = :description,
                    date_debut = :date_debut,
                    date_fin = :date_fin,
                    image_url = :image_url,
                    nombre_questions = :nombre_questions
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->date_debut = htmlspecialchars(strip_tags($this->date_debut));
        $this->date_fin = htmlspecialchars(strip_tags($this->date_fin));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->nombre_questions = (int)$this->nombre_questions;
        $this->id = (int)$this->id;

        // Liaison des paramètres
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_debut', $this->date_debut);
        $stmt->bindParam(':date_fin', $this->date_fin);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':nombre_questions', $this->nombre_questions);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Supprimer un événement
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = (int)$this->id;
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    /**
     * Récupérer les événements avec leurs questions et réponses (pour les quiz)
     */
    public function readWithQuestions() {
        $query = "SELECT e.*, 
                         q.id as question_id, q.texte_question, q.ordre as question_ordre,
                         r.id as reponse_id, r.texte_reponse, r.est_correcte, r.ordre as reponse_ordre
                  FROM " . $this->table . " e
                  LEFT JOIN questions q ON e.id = q.evenement_id
                  LEFT JOIN reponses r ON q.id = r.question_id
                  WHERE e.id = :id
                  ORDER BY q.ordre, r.ordre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>