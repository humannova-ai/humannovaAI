<?php
require_once __DIR__ . '/config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Ajouter la colonne created_at si elle n'existe pas
    $checkColumn = $conn->query("SHOW COLUMNS FROM user LIKE 'created_at'");
    if ($checkColumn->rowCount() == 0) {
        $conn->exec("ALTER TABLE user ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Colonne 'created_at' ajoutée avec succès.\n";
    } else {
        echo "La colonne 'created_at' existe déjà.\n";
    }
    
    // Ajouter la colonne updated_at si elle n'existe pas
    $checkColumn = $conn->query("SHOW COLUMNS FROM user LIKE 'updated_at'");
    if ($checkColumn->rowCount() == 0) {
        $conn->exec("ALTER TABLE user ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "Colonne 'updated_at' ajoutée avec succès.\n";
    } else {
        echo "La colonne 'updated_at' existe déjà.\n";
    }
    
    echo "Mise à jour de la base de données terminée.\n";
    
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
?>
