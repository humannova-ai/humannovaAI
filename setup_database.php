<?php
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Vérifier si la table user existe
    $checkTable = $conn->query("SHOW TABLES LIKE 'user'");
    
    if ($checkTable->rowCount() == 0) {
        // Création de la table user si elle n'existe pas
        $sql = "CREATE TABLE user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            mdp VARCHAR(255) NOT NULL,
            adresse TEXT,
            telephone VARCHAR(20),
            role ENUM('utilisateur', 'admin') DEFAULT 'utilisateur',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $conn->exec($sql);
        echo "La table 'user' a été créée avec succès.\n";
    } else {
        echo "La table 'user' existe déjà.\n";
    }
    
    // Vérifier la structure de la table
    $columns = [
        'id', 'nom', 'prenom', 'email', 'mdp', 'adresse', 
        'telephone', 'role', 'created_at', 'updated_at'
    ];
    
    $stmt = $conn->query("DESCRIBE user");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $missingColumns = array_diff($columns, $existingColumns);
    
    if (!empty($missingColumns)) {
        echo "Colonnes manquantes : " . implode(', ', $missingColumns) . "\n";
        // Ici, vous pourriez ajouter du code pour ajouter les colonnes manquantes
    } else {
        echo "La structure de la table est correcte.\n";
    }
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}

echo "Vérification de la base de données terminée.\n";
?>
