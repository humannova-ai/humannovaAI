<?php
class Database {
    private $host = "localhost";
    private $db_name = "projectphp_db";
    private $username = "root";
    private $password = "";
    public $conn;

    // Obtenir la connexion à la base de données
    public function getConnection() {
        $this->conn = null;

        try {
            // D'abord, se connecter sans spécifier de base de données
            $this->conn = new PDO(
                "mysql:host=" . $this->host,
                $this->username,
                $this->password
            );
            
            // Activer les exceptions PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vérifier si la base de données existe, sinon la créer
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Sélectionner la base de données
            $this->conn->exec("USE `{$this->db_name}`");
            
            // Créer la table user si elle n'existe pas
            $this->createTables();
            
        } catch(PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
            die();
        }

        return $this->conn;
    }
    
    // Créer les tables nécessaires
    private function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS `user` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `nom` VARCHAR(100) NOT NULL,
            `prenom` VARCHAR(100) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `mdp` VARCHAR(255) NOT NULL,
            `adresse` TEXT,
            `telephone` VARCHAR(20),
            `role` ENUM('utilisateur', 'admin') DEFAULT 'utilisateur',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $this->conn->exec($sql);
    }
}
?>