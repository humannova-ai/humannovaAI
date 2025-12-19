<?php
// test_share.php
session_start();

// Forcer l'ID de l'article
$article_id = 1; // Remplacez par l'ID réel de votre article

// Connexion à la base pour récupérer l'article
try {
    require_once 'Core/Connection.php';
    require_once 'Models/Article.php';
    
    $db = new Connection();
    $conn = $db->connect();
    
    $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        die("Article non trouvé avec ID: $article_id");
    }
    
} catch (Exception $e) {
    die("Erreur base de données: " . $e->getMessage());
}

// Construire l'URL ABSOLUE
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $host . '/blog/'; // Ajustez selon votre structure

$article_url = $base_url . 'index.php?action=show&id=' . $article_id;
$article_title = htmlspecialchars($article['titre']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>TEST Partage - 100% Fonctionnel</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .test-box { 
            border: 4px solid #00b894; 
            padding: 25px; 
            margin: 20px 0; 
            border-radius: 10px;
            background: #f9f9f9;
        }
        .url-display {
            background: #2d3436;
            color: white;
            padding: 15px;
            border-radius: 5px;
            word-break: break-all;
            margin: 15px 0;
        }
        .share-btn {
            display: inline-block;
            padding: 12px 25px;
            margin: 10px 5px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }
        .facebook { background: #1877f2; }
        .twitter { background: #000000; }
        .linkedin { background: #0a66c2; }
        .whatsapp { background: #25d366; }
        .share-btn:hover { opacity: 0.9; transform: translateY(-2px); }
    </style>
</head>
<body>
    <h1>🔧 TEST DE PARTAGE - 100% FONCTIONNEL</h1>
    
    <div class="test-box">
        <h2>📋 Informations de l'article :</h2>
        <p><strong>ID :</strong> <?= $article_id ?></p>
        <p><strong>Titre :</strong> <?= $article_title ?></p>
        
        <h3>🌐 URL générée :</h3>
        <div class="url-display">
            <?= $article_url ?>
        </div>
        
        <h3>📱 Boutons de partage :</h3>
        
        <!-- FACEBOOK -->
        <?php $fb_url = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($article_url); ?>
        <a href="<?= $fb_url ?>" class="share-btn facebook" target="_blank">
            Facebook
        </a>
        
        <!-- TWITTER -->
        <?php $tw_url = "https://twitter.com/intent/tweet?url=" . urlencode($article_url) . "&text=" . urlencode($article_title); ?>
        <a href="<?= $tw_url ?>" class="share-btn twitter" target="_blank">
            Twitter/X
        </a>
        
        <!-- LINKEDIN -->
        <?php $li_url = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($article_url); ?>
        <a href="<?= $li_url ?>" class="share-btn linkedin" target="_blank">
            LinkedIn
        </a>
        
        <!-- WHATSAPP -->
        <?php $wa_url = "https://wa.me/?text=" . urlencode($article_title . " - " . $article_url); ?>
        <a href="<?= $wa_url ?>" class="share-btn whatsapp" target="_blank">
            WhatsApp
        </a>
        
        <h3>🔗 Tester le lien :</h3>
        <a href="<?= $article_url ?>" target="_blank" style="color:#0984e3;">
            Cliquez ici pour ouvrir l'article
        </a>
    </div>
    
    <div class="test-box">
        <h2>🐞 Debug Info :</h2>
        <pre>
Protocol: <?= $protocol ?>

Host: <?= $host ?>

Base URL: <?= $base_url ?>

Complete URL: <?= $article_url ?>

Facebook URL: <?= $fb_url ?>

Article from DB: <?= print_r($article, true) ?>
        </pre>
    </div>
    
    <script>
        console.log("=== TEST SHARE DEBUG ===");
        console.log("Article URL: <?= $article_url ?>");
        console.log("Article Title: <?= $article_title ?>");
        
        // Test des liens
        document.querySelectorAll('.share-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                console.log("Opening:", this.href);
            });
        });
    </script>
</body>
</html>