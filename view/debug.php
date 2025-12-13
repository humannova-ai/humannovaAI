<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Afficher les informations de version PHP
phpinfo();

// Tester la connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=user_db', 'root', '');
    echo "<div style='color: green; margin: 20px 0; padding: 10px; background: #e8f5e9;'>
            ✅ Connexion à la base de données réussie !
          </div>";
    
    // Tester si la table user existe
    $tables = $pdo->query("SHOW TABLES LIKE 'user'")->fetchAll();
    if (count($tables) > 0) {
        echo "<div style='color: green; margin: 20px 0; padding: 10px; background: #e8f5e9;'>
                ✅ La table 'user' existe
              </div>";
    } else {
        echo "<div style='color: red; margin: 20px 0; padding: 10px; background: #ffebee;'>
                ❌ La table 'user' n'existe pas
              </div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; margin: 20px 0; padding: 10px; background: #ffebee;'>
            ❌ Erreur de connexion à la base de données : " . $e->getMessage() . "
          </div>";
}

// Tester les permissions d'écriture
$testFile = __DIR__ . '/test_write.txt';
if (file_put_contents($testFile, 'test')) {
    unlink($testFile);
    echo "<div style='color: green; margin: 20px 0; padding: 10px; background: #e8f5e9;'>
            ✅ Permissions d'écriture OK
          </div>";
} else {
    echo "<div style='color: red; margin: 20px 0; padding: 10px; background: #ffebee;'>
            ❌ Problème de permissions d'écriture dans le dossier " . __DIR__ . "
          </div>";
}

// Tester l'envoi de données POST
echo '<h3>Test d\'envoi de données :</h3>';
echo '<form method="post" action="?test=1">';
echo '  <input type="text" name="test_field" value="Test value">';
echo '  <button type="submit">Tester l\'envoi</button>';
echo '</form>';

if (isset($_GET['test'])) {
    echo '<h4>Données reçues :</h4>';
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>
