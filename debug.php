<?php
/**
 * Script de débogage
 * Placez ce fichier à la racine et accédez-y via : localhost/project/debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de connexion et de tables</h1>";

// Test 1: Connexion à la base de données
echo "<h2>1. Test de connexion</h2>";
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ <span style='color: green;'>Connexion réussie</span><br>";
    } else {
        echo "❌ <span style='color: red;'>Échec de connexion</span><br>";
        die();
    }
} catch (Exception $e) {
    echo "❌ <span style='color: red;'>Erreur: " . $e->getMessage() . "</span><br>";
    die();
}

// Test 2: Vérifier les tables
echo "<h2>2. Vérification des tables</h2>";
$tables_requises = ['evenements', 'questions', 'reponses', 'utilisateurs', 'participations', 'resultats_quiz'];

foreach ($tables_requises as $table) {
    $query = "SHOW TABLES LIKE '$table'";
    $stmt = $db->query($query);
    $exists = $stmt->rowCount() > 0;
    
    if ($exists) {
        echo "✅ <span style='color: green;'>Table '$table' existe</span><br>";
    } else {
        echo "❌ <span style='color: red;'>Table '$table' manquante</span><br>";
    }
}

// Test 3: Vérifier la structure de la table participations
echo "<h2>3. Structure de la table 'participations'</h2>";
try {
    $query = "DESCRIBE participations";
    $stmt = $db->query($query);
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (Exception $e) {
    echo "❌ <span style='color: red;'>Erreur: " . $e->getMessage() . "</span><br>";
}

// Test 4: Tester l'insertion directe
echo "<h2>4. Test d'insertion directe</h2>";
try {
    // D'abord créer un utilisateur de test
    $query = "INSERT INTO utilisateurs (nom, prenom, email) VALUES ('Test', 'Debug', 'debug@test.com')
              ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $user_id = $db->lastInsertId();
    
    if ($user_id == 0) {
        // Si lastInsertId retourne 0, récupérer l'ID existant
        $query = "SELECT id FROM utilisateurs WHERE email = 'debug@test.com'";
        $stmt = $db->query($query);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user['id'];
    }
    
    echo "✅ Utilisateur créé/récupéré (ID: $user_id)<br>";
    
    // Récupérer un événement existant
    $query = "SELECT id FROM evenements WHERE type='normal' LIMIT 1";
    $stmt = $db->query($query);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        echo "❌ <span style='color: red;'>Aucun événement de type 'normal' trouvé</span><br>";
    } else {
        $event_id = $event['id'];
        echo "✅ Événement trouvé (ID: $event_id)<br>";
        
        // Tenter une insertion
        $query = "INSERT INTO participations (utilisateur_id, evenement_id, commentaire, fichier_url, statut) 
                  VALUES (:user_id, :event_id, 'Test commentaire', NULL, 'en_attente')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':event_id', $event_id);
        
        if ($stmt->execute()) {
            $participation_id = $db->lastInsertId();
            echo "✅ <span style='color: green;'>Participation insérée avec succès (ID: $participation_id)</span><br>";
        } else {
            echo "❌ <span style='color: red;'>Erreur d'insertion</span><br>";
            print_r($stmt->errorInfo());
        }
    }
    
} catch (Exception $e) {
    echo "❌ <span style='color: red;'>Erreur: " . $e->getMessage() . "</span><br>";
}

// Test 5: Vérifier le modèle Utilisateur
echo "<h2>5. Test du modèle Utilisateur</h2>";
try {
    require_once 'models/Utilisateur.php';
    
    $utilisateur = new Utilisateur($db);
    $utilisateur->nom = "TestModele";
    $utilisateur->prenom = "Debug";
    $utilisateur->email = "testmodele@debug.com";
    
    if ($utilisateur->getOrCreate()) {
        echo "✅ <span style='color: green;'>Modèle Utilisateur fonctionne (ID: {$utilisateur->id})</span><br>";
    } else {
        echo "❌ <span style='color: red;'>Échec du modèle Utilisateur</span><br>";
    }
} catch (Exception $e) {
    echo "❌ <span style='color: red;'>Erreur: " . $e->getMessage() . "</span><br>";
}

echo "<h2>✅ Tests terminés</h2>";
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 20px; border-bottom: 2px solid #ccc; padding-bottom: 5px; }
    table { border-collapse: collapse; margin-top: 10px; }
    th { background: #f0f0f0; }
</style>