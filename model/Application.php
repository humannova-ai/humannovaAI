<?php
/**
 * Modèle Application
 * Gestion des opérations CRUD pour les candidatures
 */

class Application {
    private $conn;
    private $table = "applications";

    // Propriétés
    public $id;
    public $job_id;
    public $user_id;
    public $name;
    public $email;
    public $cover;
    public $cv_filename;
    public $status;
    public $created_at;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Récupérer toutes les candidatures avec infos du job
     */
    public function readAll() {
        $query = "SELECT a.*, j.title as job_title, j.company, u.nom, u.prenom 
                  FROM " . $this->table . " a 
                  LEFT JOIN jobs j ON a.job_id = j.id 
                  LEFT JOIN user u ON a.user_id = u.id 
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer une candidature par ID
     */
    public function readOne() {
        $query = "SELECT a.*, j.title as job_title, j.company, u.nom, u.prenom 
                  FROM " . $this->table . " a 
                  LEFT JOIN jobs j ON a.job_id = j.id 
                  LEFT JOIN user u ON a.user_id = u.id 
                  WHERE a.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->job_id = $row['job_id'];
            $this->user_id = $row['user_id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->cover = $row['cover'];
            $this->cv_filename = $row['cv_filename'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }

    /**
     * Récupérer les candidatures pour un job spécifique
     */
    public function readByJob($job_id) {
        $query = "SELECT a.*, u.nom, u.prenom, u.email as user_email 
                  FROM " . $this->table . " a 
                  LEFT JOIN user u ON a.user_id = u.id 
                  WHERE a.job_id = :job_id 
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':job_id', $job_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Créer une candidature
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET job_id = :job_id,
                    user_id = :user_id,
                    name = :name,
                    email = :email,
                    cover = :cover,
                    cv_filename = :cv_filename,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->cover = htmlspecialchars(strip_tags($this->cover));
        $this->cv_filename = htmlspecialchars(strip_tags($this->cv_filename));

        // Liaison des paramètres
        $stmt->bindParam(':job_id', $this->job_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':cover', $this->cover);
        $stmt->bindParam(':cv_filename', $this->cv_filename);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour le statut d'une candidature
     */
    public function updateStatus() {
        $query = "UPDATE " . $this->table . "
                SET status = :status
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Supprimer une candidature
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    /**
     * Compter le nombre total de candidatures
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Compter les candidatures par statut
     */
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Compter les candidatures par job
     */
    public function countByJob($job_id) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE job_id = :job_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':job_id', $job_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>