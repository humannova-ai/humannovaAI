<!DOCTYPE html>
<html>
<head>
    <title>Test Réactions</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 2px solid #ddd; border-radius: 8px; }
        .success { background: #e8f5e9; border-color: #4caf50; }
        .error { background: #ffebee; border-color: #f44336; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🧪 Test du Système de Réactions</h1>

    <div class="test-section">
        <h2>1. Test de connexion à la base de données</h2>
        <?php
        define('ROOT_PATH', __DIR__);
        require_once ROOT_PATH . '/shared/Core/Connection.php';
        
        try {
            $db = new Connection();
            $conn = $db->connect();
            echo '<p class="success">✅ Connexion réussie à la base de données</p>';
            
            // Vérifier si la table reactions existe
            $stmt = $conn->query("SHOW TABLES LIKE 'reactions'");
            if ($stmt->rowCount() > 0) {
                echo '<p class="success">✅ Table "reactions" existe</p>';
                
                // Compter les réactions
                $count = $conn->query("SELECT COUNT(*) FROM reactions")->fetchColumn();
                echo '<p>📊 Nombre de réactions dans la base: ' . $count . '</p>';
            } else {
                echo '<p class="error">❌ Table "reactions" n\'existe pas</p>';
                echo '<p>➡️ Exécutez le fichier create_reactions_table.sql dans phpMyAdmin</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>2. Test du Model Reaction</h2>
        <?php
        try {
            require_once ROOT_PATH . '/shared/Models/Reaction.php';
            $reaction = new Reaction();
            echo '<p class="success">✅ Model Reaction chargé</p>';
            
            $emojis = $reaction->getAvailableEmojis();
            echo '<p>📝 Emojis disponibles: ' . implode(' ', $emojis) . '</p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>3. Test du Controller</h2>
        <?php
        try {
            require_once __DIR__ . '/blog/Controllers/ReactionController.php';
            echo '<p class="success">✅ ReactionController chargé</p>';
            echo '<p>🔗 URL du contrôleur: <a href="blog/index.php?controller=reaction&action=handle&article_id=1">Tester</a></p>';
        } catch (Exception $e) {
            echo '<p class="error">❌ Erreur: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>4. Test AJAX en direct</h2>
        <button onclick="testGetReactions()">Tester GET (récupérer réactions)</button>
        <button onclick="testAddReaction()">Tester POST (ajouter réaction)</button>
        <div id="ajax-result"></div>
    </div>

    <script>
    async function testGetReactions() {
        const result = document.getElementById('ajax-result');
        result.innerHTML = '<p>🔄 Chargement...</p>';
        
        try {
            const response = await fetch('blog/index.php?controller=reaction&action=handle&article_id=1');
            const data = await response.json();
            
            result.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (error) {
            result.innerHTML = '<p class="error">❌ Erreur: ' + error.message + '</p>';
        }
    }

    async function testAddReaction() {
        const result = document.getElementById('ajax-result');
        result.innerHTML = '<p>🔄 Envoi réaction...</p>';
        
        try {
            const formData = new FormData();
            formData.append('article_id', 1);
            formData.append('emoji', '👍');
            
            const response = await fetch('blog/index.php?controller=reaction&action=handle', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            result.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (error) {
            result.innerHTML = '<p class="error">❌ Erreur: ' + error.message + '</p>';
        }
    }
    </script>

    <div class="test-section">
        <h2>📋 Résumé</h2>
        <p>Si tous les tests sont verts (✅), le système est fonctionnel!</p>
        <p>Si vous voyez des erreurs rouges (❌), suivez les instructions dans REACTIONS_SETUP.md</p>
    </div>
</body>
</html>
