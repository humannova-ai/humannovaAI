<?php
/**
 * Modèle Job
 * Gestion des opérations CRUD pour les offres d'emploi
 */

class Job {
    private $conn;
    private $table = "jobs";

    // Propriétés
    public $id;
    public $user_id;
    public $title;
    public $company;
    public $salary;
    public $description;
    public $location;
    public $date_posted;
    public $category;
    public $contract_type;
    public $logo;
    public $status;
    public $created_at;
    public $updated_at;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Récupérer tous les jobs
     */
    public function readAll() {
        $query = "SELECT j.*, u.nom, u.prenom, u.email 
                  FROM " . $this->table . " j 
                  LEFT JOIN user u ON j.user_id = u.id 
                  ORDER BY j.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer tous les jobs actifs (status = 'active')
     */
    public function readActive() {
        $query = "SELECT j.*, u.nom, u.prenom, u.email 
                  FROM " . $this->table . " j 
                  LEFT JOIN user u ON j.user_id = u.id 
                  WHERE j.status = 'active' 
                  ORDER BY j.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer un job par ID
     */
    public function readOne() {
        $query = "SELECT j.*, u.nom, u.prenom, u.email 
                  FROM " . $this->table . " j 
                  LEFT JOIN user u ON j.user_id = u.id 
                  WHERE j.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->user_id = $row['user_id'];
            $this->title = $row['title'];
            $this->company = $row['company'];
            $this->salary = $row['salary'];
            $this->description = $row['description'];
            $this->location = $row['location'];
            $this->date_posted = $row['date_posted'];
            $this->category = $row['category'];
            $this->contract_type = $row['contract_type'];
            $this->logo = $row['logo'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        
        return false;
    }

    /**
     * Créer un job
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET user_id = :user_id,
                    title = :title,
                    company = :company,
                    salary = :salary,
                    description = :description,
                    location = :location,
                    date_posted = :date_posted,
                    category = :category,
                    contract_type = :contract_type,
                    logo = :logo,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->salary = htmlspecialchars(strip_tags($this->salary));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->contract_type = htmlspecialchars(strip_tags($this->contract_type));
        $this->logo = htmlspecialchars(strip_tags($this->logo));

        // Liaison des paramètres
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':salary', $this->salary);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':date_posted', $this->date_posted);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':contract_type', $this->contract_type);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour un job
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                SET title = :title,
                    company = :company,
                    salary = :salary,
                    description = :description,
                    location = :location,
                    date_posted = :date_posted,
                    category = :category,
                    contract_type = :contract_type,
                    logo = :logo,
                    status = :status,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->salary = htmlspecialchars(strip_tags($this->salary));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->contract_type = htmlspecialchars(strip_tags($this->contract_type));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->id = (int)$this->id;

        // Liaison des paramètres
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':company', $this->company);
        $stmt->bindParam(':salary', $this->salary);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':date_posted', $this->date_posted);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':contract_type', $this->contract_type);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Supprimer un job
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = (int)$this->id;
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    /**
     * Compter le nombre total de jobs
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Compter les jobs par statut
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
     * Search jobs based on keywords
     */
    public function search($keywords) {
        $query = "SELECT * FROM " . $this->table . " WHERE 
                  title LIKE :keywords OR 
                  description LIKE :keywords OR 
                  company LIKE :keywords";
        $stmt = $this->conn->prepare($query);
        $keywords = "%" . htmlspecialchars(strip_tags($keywords)) . "%";
        $stmt->bindParam(':keywords', $keywords);
        $stmt->execute();
        return $stmt;
    }
}
?>