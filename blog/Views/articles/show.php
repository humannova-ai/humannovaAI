<?php
// Views/articles/show.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists(ROOT_PATH . '/blog/Views/partials/_navbar.php')) include ROOT_PATH . '/blog/Views/partials/_navbar.php';

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
    
    <link rel="stylesheet" href="Views/css/theme.css">
    <style>
        .article-actions {
            display: flex;
            gap: 0;
            padding: 0;
            margin: 30px 0;
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 20px;
            background: #1a1a1a;
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn:last-child {
            border-right: none;
        }
        
        .action-btn:hover {
            background: #2a2a2a;
            transform: translateY(-1px);
        }
        
        .action-btn:active {
            transform: translateY(0);
        }
        
        .action-btn span {
            font-size: 18px;
        }
        
        .emoji-reaction-btn:hover {
            transform: scale(1.2) rotate(10deg);
            border-color: rgba(255,255,255,1) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: white !important;
        }
        
        .emoji-reaction-btn:active {
            transform: scale(0.9);
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(0.9); }
            50% { transform: scale(1.3); }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        #reactions-panel.active {
            animation: slideDown 0.3s ease;
        }
        
        #share-panel.active {
            animation: slideDown 0.3s ease;
        }
        
        #share-panel button:hover {
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        #share-panel button:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">
    
    <div class="article-container">
        <h1><?= htmlspecialchars($article['titre']) ?></h1>
        
        <!-- Contenu -->
        <div class="article-content">
            <?= nl2br(htmlspecialchars($article['contenu'] ?? '')) ?>
        </div>
        
        <!-- Action Buttons -->
        <div class="article-actions">
            <button class="action-btn" onclick="toggleReactionsPanel()">
                <span>👍</span> J'aime
            </button>
            <button class="action-btn" onclick="openCommentsModal()">
                <span>💬</span> Commenter
            </button>
            <button class="action-btn" onclick="shareArticle()">
                <span>📤</span> Partager
            </button>
        </div>
        
        <!-- Reactions Panel (Hidden by default) -->
        <div id="reactions-panel" style="display: none; margin: 20px 0; padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; text-align: center; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
            <p style="margin-bottom: 20px; font-weight: 700; color: white; font-size: 18px;">✨ Choisissez votre réaction</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('👍', event)" data-emoji="👍" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">👍</button>
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('❤️', event)" data-emoji="❤️" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">❤️</button>
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('😮', event)" data-emoji="😮" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">😮</button>
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('😄', event)" data-emoji="😄" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">😄</button>
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('🔥', event)" data-emoji="🔥" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">🔥</button>
                <button class="emoji-reaction-btn" onclick="sendQuickReaction('👏', event)" data-emoji="👏" style="font-size: 36px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">👏</button>
            </div>
            <div id="reaction-stats" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(255,255,255,0.3); font-size: 15px; color: white; font-weight: 600;"></div>
        </div>
        
        <!-- Share Panel (Hidden by default) -->
        <div id="share-panel" style="display: none; margin: 20px 0; padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; text-align: center; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);">
            <p style="margin-bottom: 20px; font-weight: 700; color: white; font-size: 18px;">📣 Partager cet article</p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button onclick="shareOnPlatform('facebook')" style="font-size: 32px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: #1877f2; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-family: Arial, sans-serif;">f</button>
                <button onclick="shareOnPlatform('twitter')" style="font-size: 32px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: #000000; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">𝕏</button>
                <button onclick="shareOnPlatform('linkedin')" style="font-size: 26px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: #0077b5; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-family: Arial, sans-serif;">in</button>
                <button onclick="shareOnPlatform('whatsapp')" style="font-size: 42px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: #25D366; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white;">💬</button>
                <button onclick="shareOnPlatform('copy')" style="font-size: 32px; padding: 15px; border: 3px solid rgba(255,255,255,0.3); background: #546e7a; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; color: white;">📄</button>
            </div>
            <div id="share-feedback" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid rgba(255,255,255,0.3); font-size: 15px; color: white; font-weight: 600;"></div>
        </div>
        
        <!-- Section commentaires -->
        <?php if(file_exists('Views/articles/comments_section.php')): ?>
            <?php $articleId = $article['id'] ?? 0; ?>
            <?php include 'Views/articles/comments_section.php'; ?>
        <?php endif; ?>
    </div>
    
    <!-- Scripts JavaScript -->
    <?php if (file_exists(ROOT_PATH . '/blog/Views/partials/chat.php')) include ROOT_PATH . '/blog/Views/partials/chat.php'; ?>

    <script>
    // Toggle Reactions Panel
    function toggleReactionsPanel() {
        const panel = document.getElementById('reactions-panel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            panel.classList.add('active');
            loadReactionStats();
        } else {
            panel.style.display = 'none';
            panel.classList.remove('active');
        }
    }
    
    // Send Quick Reaction
    async function sendQuickReaction(emoji, e) {
        const articleId = <?= $article['id'] ?? 0 ?>;
        if (!articleId) {
            alert('Erreur: Article non trouvé');
            return;
        }
        
        console.log('Sending reaction:', emoji, 'for article:', articleId);
        
        try {
            const formData = new FormData();
            formData.append('article_id', articleId);
            formData.append('type', 'like');
            formData.append('auteur', '<?= $_SESSION['user_name'] ?? 'Utilisateur' ?>');
            formData.append('email', '<?= $_SESSION['user_email'] ?? 'user@example.com' ?>');
            formData.append('message', emoji);
            
            const response = await fetch(window.location.origin + (window.SITE_BASE || '') + '/blog/index.php?controller=interaction&action=create', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response status:', response.status);
            const text = await response.text();
            console.log('Response:', text);
            
            if (response.ok) {
                // Visual feedback
                if (e && e.target) {
                    e.target.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        e.target.style.transform = 'scale(1)';
                    }, 200);
                }
                
                // Show success message
                const statsDiv = document.getElementById('reaction-stats');
                statsDiv.innerHTML = '<span style="color: #4caf50;">✅ Réaction ajoutée avec succès!</span>';
                
                setTimeout(() => {
                    loadReactionStats();
                }, 1000);
            } else {
                alert('Erreur lors de l\'ajout de la réaction');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur: ' + error.message);
        }
    }
    
    // Load Reaction Statistics
    async function loadReactionStats() {
        const articleId = <?= $article['id'] ?? 0 ?>;
        const statsDiv = document.getElementById('reaction-stats');
        
        if (!statsDiv) return;
        
        try {
            const response = await fetch(window.location.origin + (window.SITE_BASE || '') + '/blog/index.php?controller=interaction&action=getStats&article_id=' + articleId);
            const text = await response.text();
            console.log('Stats response:', text);
            
            const data = JSON.parse(text);
            
            if (data.success && data.stats) {
                const total = data.stats.total || 0;
                const emojis = data.stats.emojis || [];
                
                let html = '<strong>Total: ' + total + ' réaction' + (total > 1 ? 's' : '') + '</strong>';
                
                if (emojis.length > 0) {
                    html += '<div style="margin-top: 10px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">';
                    const emojiCounts = {};
                    emojis.forEach(e => {
                        emojiCounts[e] = (emojiCounts[e] || 0) + 1;
                    });
                    Object.keys(emojiCounts).forEach(emoji => {
                        html += '<span style="font-size: 20px;">' + emoji + ' ' + emojiCounts[emoji] + '</span>';
                    });
                    html += '</div>';
                }
                
                statsDiv.innerHTML = html;
            } else {
                statsDiv.innerHTML = '<span>Soyez le premier à réagir!</span>';
            }
        } catch (error) {
            console.error('Could not load stats:', error);
            statsDiv.innerHTML = '<span>Chargement...</span>';
        }
    }
    
    // Share Article
    function shareArticle() {
        const panel = document.getElementById('share-panel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            panel.classList.add('active');
            // Close reactions panel if open
            const reactionsPanel = document.getElementById('reactions-panel');
            if (reactionsPanel) {
                reactionsPanel.style.display = 'none';
                reactionsPanel.classList.remove('active');
            }
        } else {
            panel.style.display = 'none';
            panel.classList.remove('active');
        }
    }
    
    function shareOnPlatform(platform) {
        const url = window.location.href;
        const title = document.querySelector('h1').textContent;
        const feedbackDiv = document.getElementById('share-feedback');
        
        let shareUrl = '';
        
        switch(platform) {
            case 'facebook':
                shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url);
                window.open(shareUrl, '_blank', 'width=600,height=400');
                feedbackDiv.innerHTML = '<span style="color: #4caf50;">📘 Ouverture de Facebook...</span>';
                break;
            case 'twitter':
                shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(title);
                window.open(shareUrl, '_blank', 'width=600,height=400');
                feedbackDiv.innerHTML = '<span style="color: #4caf50;">𝕏 Ouverture de Twitter...</span>';
                break;
            case 'linkedin':
                shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(url);
                window.open(shareUrl, '_blank', 'width=600,height=400');
                feedbackDiv.innerHTML = '<span style="color: #4caf50;">💼 Ouverture de LinkedIn...</span>';
                break;
            case 'whatsapp':
                shareUrl = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + url);
                window.open(shareUrl, '_blank');
                feedbackDiv.innerHTML = '<span style="color: #4caf50;">📱 Ouverture de WhatsApp...</span>';
                break;
            case 'copy':
                navigator.clipboard.writeText(url).then(() => {
                    feedbackDiv.innerHTML = '<span style="color: #4caf50;">✅ Lien copié dans le presse-papiers!</span>';
                    setTimeout(() => {
                        feedbackDiv.innerHTML = '';
                    }, 3000);
                }).catch(() => {
                    feedbackDiv.innerHTML = '<span style="color: #ff6b6b;">❌ Erreur lors de la copie</span>';
                });
                break;
        }
        
        if (platform !== 'copy') {
            setTimeout(() => {
                feedbackDiv.innerHTML = '';
            }, 3000);
        }
    }
    </script>
</body>
</html>