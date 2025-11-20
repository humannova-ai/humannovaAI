<?php
/**
 * Configuration de la connexion à la base de données
 * Utilise PDO pour une meilleure sécurité et flexibilité
 */

class Database {
    private $host = "localhost";
    private $db_name = "events_management1";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Obtenir la connexion à la base de données
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            
            // Configuration PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>