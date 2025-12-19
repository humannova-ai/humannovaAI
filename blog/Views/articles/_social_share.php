<?php
// Views/articles/_social_share.php - VERSION ULTRA SIMPLE

// ============================================
// MÉTHODE DIRECTE : RÉCUPÉRER LES DONNÉES NOUS-MÊMES
// ============================================

// 1. Récupérer l'ID de l'article depuis l'URL ou la session
$article_id = 0;

// Essayer depuis GET
if (isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
} 
// Essayer depuis la session
elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['current_article_id'])) {
    $article_id = $_SESSION['current_article_id'];
}

// Si toujours 0, essayer de le deviner depuis le référent
if ($article_id == 0 && isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
    if (preg_match('/id=(\d+)/', $referer, $matches)) {
        $article_id = (int)$matches[1];
    }
}

// ============================================
// 2. SI ON A UN ID, CHARGER L'ARTICLE
// ============================================
$article_title = "Article intéressant";
$article_data = null;

if ($article_id > 0) {
    try {
        // Chemin absolu vers les modèles
        require_once __DIR__ . '/../../Models/Article.php';
        $articleModel = new Article();
        $article_data = $articleModel->readById($article_id);
        
        if ($article_data && isset($article_data['titre'])) {
            $article_title = htmlspecialchars($article_data['titre']);
        }
    } catch (Exception $e) {
        // Silencieux en production
        error_log("Social share error: " . $e->getMessage());
    }
}

// ============================================
// 3. CONSTRUIRE L'URL ABSOLUE
// ============================================
// Méthode SIMPLE et FIABLE
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$protocol = $is_https ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST']; // localhost ou votre domaine

// VOTRE CHEMIN DE BASE - À ADAPTER
// Normalement c'est soit "/blog" soit "/" selon votre installation
$base_path = '/blog'; // ← CHANGEZ CE CI SI NÉCESSAIRE

// URL complète
$article_url = $protocol . $host . $base_path . '/index.php?action=show&id=' . $article_id;

// ============================================
// 4. AFFICHER - FORCÉMENT VISIBLE
// ============================================
?>
<!-- DÉBUT SECTION PARTAGE -->
<div class="social-share-section" style="
    border: 3px solid #00b894;
    background: white;
    padding: 25px;
    margin: 30px 0;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
">
    
    <h3 style="color: #2d3436; margin-top: 0; font-size: 1.5em;">
        📢 PARTAGER CET ARTICLE
    </h3>
    
    <p style="color: #636e72; margin-bottom: 25px;">
        Partagez cet article sur vos réseaux sociaux :
    </p>
    
    <!-- URL VISIBLE -->
    <div style="
        background: #f1f2f6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #0984e3;
    ">
        <strong style="color: #2d3436; display: block; margin-bottom: 5px;">
            Lien à partager :
        </strong>
        <code style="
            color: #0984e3;
            word-break: break-all;
            font-size: 14px;
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin: 5px 0;
            border: 1px solid #ddd;
        ">
            <?= htmlspecialchars($article_url) ?>
        </code>
        <br>
        <small style="color: #636e72;">
            (Titre : "<?= $article_title ?>")
        </small>
    </div>
    
    <!-- BOUTONS DE PARTAGE -->
    <div class="share-buttons" style="
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 20px 0;
    ">
        <?php
        // Générer les URLs de partage
        $encoded_url = urlencode($article_url);
        $encoded_title = urlencode($article_title);
        
        $share_platforms = [
            'facebook' => [
                'url' => "https://www.facebook.com/sharer/sharer.php?u=$encoded_url&quote=$encoded_title",
                'color' => '#1877f2',
                'icon' => 'f',
                'text' => 'Facebook'
            ],
            'twitter' => [
                'url' => "https://twitter.com/intent/tweet?url=$encoded_url&text=$encoded_title",
                'color' => '#000000',
                'icon' => '𝕏',
                'text' => 'Twitter/X'
            ],
            'linkedin' => [
                'url' => "https://www.linkedin.com/sharing/share-offsite/?url=$encoded_url",
                'color' => '#0a66c2',
                'icon' => 'in',
                'text' => 'LinkedIn'
            ],
            'whatsapp' => [
                'url' => "https://wa.me/?text=" . urlencode("$article_title - $article_url"),
                'color' => '#25d366',
                'icon' => '📱',
                'text' => 'WhatsApp'
            ]
        ];
        
        foreach ($share_platforms as $platform => $data):
        ?>
        <a href="<?= $data['url'] ?>" 
           target="_blank"
           rel="noopener noreferrer"
           style="
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 14px 22px;
                background: <?= $data['color'] ?>;
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s;
                min-width: 150px;
                justify-content: center;
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
           "
           onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';"
           onclick="console.log('Sharing to <?= $platform ?>: <?= $data['url'] ?>');">
            <span style="font-size: 20px; font-weight: bold;"><?= $data['icon'] ?></span>
            <span><?= $data['text'] ?></span>
        </a>
        <?php endforeach; ?>
        
        <!-- BOUTON COPIER -->
        <button onclick="copyLinkNow('<?= addslashes($article_url) ?>')"
                style="
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    padding: 14px 22px;
                    background: linear-gradient(135deg, #636e72, #2d3436);
                    color: white;
                    border: none;
                    border-radius: 10px;
                    font-weight: 600;
                    cursor: pointer;
                    min-width: 150px;
                    justify-content: center;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                    transition: all 0.3s;
                "
                onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 16px rgba(0,0,0,0.2)';"
                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';">
            <span style="font-size: 20px;">📋</span>
            <span>Copier le lien</span>
        </button>
    </div>
    
    <!-- MESSAGE DE CONFIRMATION -->
    <div id="share-message" style="
        display: none;
        padding: 15px;
        margin-top: 15px;
        border-radius: 8px;
        background: #00b894;
        color: white;
        font-weight: 500;
        text-align: center;
    ">
        ✅ Lien copié avec succès !
    </div>
</div>
<!-- FIN SECTION PARTAGE -->

<script>
// Fonction SIMPLE pour copier le lien
function copyLinkNow(url) {
    // Créer un élément temporaire
    const tempInput = document.createElement('textarea');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    
    // Sélectionner et copier
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Pour mobile
    
    try {
        const successful = document.execCommand('copy');
        const msg = document.getElementById('share-message');
        
        if (successful) {
            // Afficher le message de succès
            msg.style.display = 'block';
            msg.style.background = '#00b894';
            msg.textContent = '✅ Lien copié avec succès !';
            
            // Masquer après 3 secondes
            setTimeout(() => {
                msg.style.display = 'none';
            }, 3000);
            
            console.log('URL copiée:', url);
        } else {
            msg.style.display = 'block';
            msg.style.background = '#e17055';
            msg.textContent = '❌ Échec de la copie. Copiez manuellement : ' + url;
        }
    } catch (err) {
        console.error('Copy error:', err);
        alert('Copiez manuellement : ' + url);
    }
    
    // Nettoyer
    document.body.removeChild(tempInput);
}

// Debug dans la console
console.log('=== SOCIAL SHARE LOADED ===');
console.log('Article URL:', '<?= $article_url ?>');
console.log('Article Title:', '<?= $article_title ?>');
console.log('Article ID:', <?= $article_id ?>);

// Tester immédiatement
window.addEventListener('load', function() {
    console.log('Page loaded, share section ready');
    
    // Vérifier visuellement
    const shareSection = document.querySelector('.social-share-section');
    if (shareSection) {
        console.log('Share section found in DOM');
        
        // Flash pour confirmation visuelle
        shareSection.style.transition = 'border-color 0.5s';
        setTimeout(() => {
            shareSection.style.borderColor = '#00cec9';
            setTimeout(() => {
                shareSection.style.borderColor = '#00b894';
            }, 1000);
        }, 500);
    } else {
        console.error('Share section NOT found in DOM!');
    }
});
</script>

<!-- DEBUG INFO (toujours visible) -->
<div style="
    margin-top: 20px;
    padding: 15px;
    background: #ffeaa7;
    border-radius: 8px;
    border: 2px dashed #fdcb6e;
    font-size: 14px;
    color: #2d3436;
">
    <strong>🐛 DEBUG INFO :</strong><br>
    • Article ID: <strong><?= $article_id ?></strong><br>
    • URL: <code><?= htmlspecialchars($article_url) ?></code><br>
    • Base Path: <?= $base_path ?><br>
    • Protocol: <?= $protocol ?><br>
    • Host: <?= $host ?>
</div>