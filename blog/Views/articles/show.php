<?php
 include 'social_share_fixed.php';
// Views/articles/show.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = $_SESSION['errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
$success = $_SESSION['success'] ?? null;

unset($_SESSION['errors'], $_SESSION['form_data'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['titre']) ?> - PRO MANAGE AI</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="Views/css/theme.css">
    <link rel="stylesheet" href="Views/css/reactions.css">
    <link rel="stylesheet" href="Views/css/social_share.css">
    <style>
        .theme-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            transition: .4s;
        }
        
        .theme-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: .4s;
        }
        
        input:checked + .theme-slider {
            background-color: #2196F3;
        }
        
        input:checked + .theme-slider:before {
            transform: translateX(30px);
        }
        
        .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
        }
        
        .theme-icon.sun {
            left: 8px;
        }
        
        .theme-icon.moon {
            right: 8px;
        }
    </style>
</head>
<body data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">
    <!-- Switch thème -->
    <?php include 'Views/layout/_theme_switcher.php'; ?>
    
    <!-- Barre de progression de lecture -->
    <div class="reading-progress" id="reading-progress"></div>
    
    <div class="article-container">
        <h1><?= htmlspecialchars($article['titre']) ?></h1>
        
        <!-- Meta informations -->
        <div class="article-meta">
            <span class="meta-item">📅 <?= date('d/m/Y', strtotime($article['date_creation'] ?? 'now')) ?></span>
            <span class="meta-item">👁️ <?= $article['views'] ?? 0 ?> vues</span>
            <span class="meta-item">⏱️ <?= $article['reading_time'] ?? 5 ?> min</span>
            <?php if(isset($article['tags']) && !empty($article['tags'])): ?>
                <span class="meta-item">🏷️ <?= htmlspecialchars($article['tags']) ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Image si disponible -->
        <?php if(isset($article['image']) && !empty($article['image'])): ?>
            <div class="article-image">
                <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>">
            </div>
        <?php endif; ?>
        
        <!-- Contenu -->
        <div class="article-content">
            <?= nl2br(htmlspecialchars($article['contenu'] ?? '')) ?>
        </div>
        
        <!-- Section réactions -->
        <?php include 'Views/articles/_reactions.php'; ?>
        
        <!-- Section partage social -->
        <?php include 'Views/articles/_social_share.php'; ?>
        
        <!-- Section commentaires -->
        <?php if(file_exists('Views/articles/comments_section.php')): ?>
            <div class="comments-section">
                <h2>Interactions</h2>
                <?php include 'Views/articles/comments_section.php'; ?>
            </div>
        <?php endif; ?>
        
        <!-- Navigation -->
        <div class="article-navigation">
            <a href="index.php?controller=article&action=index" class="btn-back">
                ← Retour aux articles
            </a>
        </div>
    </div>
    
    <!-- Scripts JavaScript -->
    <script>
    // Barre de progression de lecture
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        const progressBar = document.getElementById('reading-progress');
        if (progressBar) {
            progressBar.style.width = scrolled + "%";
        }
    });
    
    // Gestion du thème
    document.addEventListener('themeChanged', (e) => {
        console.log('Thème changé:', e.detail.theme);
    });
    </script>
</body>
</html>