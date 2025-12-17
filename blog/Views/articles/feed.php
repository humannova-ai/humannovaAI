<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Feed</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Rajdhani', sans-serif;
            background: #0a0a0a;
            color: #e4e6eb;
            min-height: 100vh;
        }
        
        /* Container principal */
        .feed-container {
            max-width: 680px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Post Creation Box */
        .create-post-box {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }
        
        .create-post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff88, #00ccff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            color: #0a0a0a;
        }
        
        .create-post-input {
            flex: 1;
            background: #121212;
            border: 1px solid #3a3a3a;
            border-radius: 25px;
            padding: 12px 20px;
            color: #e4e6eb;
            font-size: 16px;
            font-family: 'Rajdhani', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .create-post-input:hover {
            background: #1a1a1a;
            border-color: #00ff88;
        }
        
        .create-post-actions {
            display: flex;
            justify-content: space-around;
            padding-top: 15px;
            border-top: 1px solid #3a3a3a;
            margin-top: 15px;
        }
        
        .action-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: #b0b3b8;
            font-size: 15px;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            background: #2a2a2a;
            color: #00ff88;
        }
        
        .action-button svg {
            width: 24px;
            height: 24px;
        }
        
        /* Post Card */
        .post-card {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }
        
        .post-card:hover {
            border-color: #00ff88;
            box-shadow: 0 4px 12px rgba(0, 255, 136, 0.1);
        }
        
        .post-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .post-author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff0080, #ff8c00);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }
        
        .post-author-info {
            flex: 1;
        }
        
        .post-author-name {
            font-weight: 600;
            font-size: 16px;
            color: #e4e6eb;
        }
        
        .post-date {
            font-size: 13px;
            color: #8a8d91;
        }
        
        .post-options {
            color: #8a8d91;
            cursor: pointer;
            font-size: 24px;
            padding: 5px;
        }
        
        .post-options:hover {
            color: #00ff88;
        }
        
        .post-content {
            padding: 0 20px 15px;
        }
        
        .post-title {
            font-size: 20px;
            font-weight: 600;
            color: #e4e6eb;
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }
        
        .post-text {
            color: #b0b3b8;
            line-height: 1.6;
            font-size: 15px;
        }
        
        .post-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }
        
        .post-tag {
            background: #2a2a2a;
            color: #00ff88;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .post-stats {
            padding: 0 20px 12px;
            display: flex;
            justify-content: space-between;
            color: #8a8d91;
            font-size: 14px;
        }
        
        .post-stats span {
            cursor: pointer;
        }
        
        .post-stats span:hover {
            text-decoration: underline;
            color: #00ff88;
        }
        
        .article-actions {
            display: flex;
            gap: 0;
            padding: 0;
            margin: 15px 0;
            background: #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-top: 1px solid #3a3a3a;
        }
        
        .article-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: #1a1a1a;
            border: none;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            color: #b0b3b8;
            font-size: 15px;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .article-action-btn:last-child {
            border-right: none;
        }
        
        .article-action-btn:hover {
            background: #2a2a2a;
            color: #00ff88;
            transform: translateY(-1px);
        }
        
        .article-action-btn:active {
            transform: translateY(0);
        }
        
        .article-action-btn span {
            font-size: 18px;
        }
        
        .article-action-btn.active {
            color: #00ff88;
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
        
        .reactions-panel.active,
        .share-panel.active {
            animation: slideDown 0.3s ease;
        }
        
        .share-panel button:hover {
            transform: scale(1.15) rotate(-5deg);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .share-panel button:active {
            transform: scale(0.95);
        }
        
        /* Comments Section */
        .comments-section {
            border-top: 1px solid #3a3a3a;
            padding: 15px 20px;
            display: none;
        }
        
        .comments-section.show {
            display: block;
        }
        
        .comment {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }
        
        .comment-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
        }
        
        .comment-content {
            flex: 1;
            background: #121212;
            border-radius: 12px;
            padding: 10px 12px;
        }
        
        .comment-author {
            font-weight: 600;
            font-size: 14px;
            color: #e4e6eb;
            margin-bottom: 4px;
        }
        
        .comment-text {
            color: #b0b3b8;
            font-size: 14px;
            line-height: 1.4;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .post-card {
            animation: fadeIn 0.4s ease-out;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #3a3a3a;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #00ff88;
        }
        
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-overlay.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0, 255, 136, 0.2);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #3a3a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 20px;
            font-weight: 600;
            color: #e4e6eb;
            font-family: 'Orbitron', sans-serif;
        }
        
        .modal-close {
            background: transparent;
            border: none;
            font-size: 28px;
            color: #8a8d91;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .modal-close:hover {
            background: #2a2a2a;
            color: #00ff88;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #e4e6eb;
            font-weight: 500;
            font-size: 15px;
        }
        
        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 15px;
            background: #121212;
            border: 1px solid #3a3a3a;
            border-radius: 8px;
            color: #e4e6eb;
            font-size: 15px;
            font-family: 'Rajdhani', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 0 2px rgba(0, 255, 136, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .category-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }
        
        .category-tag {
            background: #2a2a2a;
            color: #00ff88;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .category-tag:hover {
            background: #00ff88;
            color: #0a0a0a;
        }
        
        .category-tag.selected {
            background: #00ff88;
            color: #0a0a0a;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #3a3a3a;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            font-family: 'Rajdhani', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-cancel {
            background: transparent;
            color: #8a8d91;
        }
        
        .btn-cancel:hover {
            background: #2a2a2a;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #00ff88, #00ccff);
            color: #0a0a0a;
            font-weight: 600;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 255, 136, 0.4);
        }
    </style>
</head>
<body>
    <div class="feed-container">
        <!-- Create Post Box -->
        <div class="create-post-box">
            <div class="create-post-header">
                <div class="user-avatar"><?= isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : 'U' ?></div>
                <input type="text" 
                       class="create-post-input" 
                       placeholder="Quoi de neuf ?"
                       onclick="openCreateModal()"
                       readonly>
            </div>
            <div class="create-post-actions">
                <button class="action-button" onclick="openCreateModal()">
                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2v-3H9v-2h3V9h2v3h3v2h-3v3z"/></svg>
                    Créer un article
                </button>
                <button class="action-button" onclick="openCreateModal()">
                    <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
                    Partager une idée
                </button>
            </div>
        </div>
        
        <!-- Modal Créer un Article -->
        <div class="modal-overlay" id="createArticleModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">✨ Créer un Article</h2>
                    <button class="modal-close" onclick="closeCreateModal()">&times;</button>
                </div>
                <form method="POST" action="index.php?controller=article&action=store" id="createArticleForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label" for="titre">Titre de l'article <span style="color: #ff3333;">*</span></label>
                            <input type="text" id="titre" name="titre" class="form-input" placeholder="Entrez un titre accrocheur..." required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="contenu">Contenu <span style="color: #ff3333;">*</span></label>
                            <textarea id="contenu" name="contenu" class="form-textarea" placeholder="Partagez votre histoire, vos idées..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="categorie">Catégorie</label>
                            <select id="categorie" name="categorie" class="form-select">
                                <option value="">Sélectionnez une catégorie</option>
                                <option value="Technologie">🔧 Technologie</option>
                                <option value="Lifestyle">🌟 Lifestyle</option>
                                <option value="Culture">🎨 Culture</option>
                                <option value="Science">🔬 Science</option>
                                <option value="Voyage">✈️ Voyage</option>
                                <option value="Autre">📌 Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="statut">Statut</label>
                            <select id="statut" name="statut" class="form-select">
                                <option value="brouillon">📝 Brouillon</option>
                                <option value="publie" selected>✅ Publié</option>
                                <option value="archive">📦 Archivé</option>
                            </select>
                        </div>
                        
                        <div class="category-suggestions">
                            <span class="category-tag" onclick="selectCategory('Technologie')">Technologie</span>
                            <span class="category-tag" onclick="selectCategory('Lifestyle')">Lifestyle</span>
                            <span class="category-tag" onclick="selectCategory('Culture')">Culture</span>
                            <span class="category-tag" onclick="selectCategory('Science')">Science</span>
                            <span class="category-tag" onclick="selectCategory('Voyage')">Voyage</span>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" onclick="closeCreateModal()">Annuler</button>
                        <button type="submit" class="btn btn-submit">Publier l'article</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Posts Feed -->
        <?php if (empty($articles)): ?>
            <div style="text-align: center; padding: 40px; color: #8a8d91;">
                <p>Aucun article pour le moment. Soyez le premier à publier !</p>
            </div>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
            <div class="post-card" id="post-<?= $article['id'] ?>">
                <div class="post-header">
                    <div class="post-author-avatar"><?= strtoupper(substr($article['titre'], 0, 1)) ?></div>
                    <div class="post-author-info">
                        <div class="post-author-name">Auteur</div>
                        <div class="post-date"><?= date('d M', strtotime($article['date_creation'])) ?> à <?= date('H:i', strtotime($article['date_creation'])) ?></div>
                    </div>
                    <div class="post-options">⋯</div>
                </div>
                
                <div class="post-content">
                    <h2 class="post-title"><?= htmlspecialchars($article['titre']) ?></h2>
                    <p class="post-text"><?= nl2br(htmlspecialchars(substr($article['excerpt'] ?? $article['contenu'], 0, 300))) ?><?= strlen($article['contenu']) > 300 ? '...' : '' ?></p>
                    <?php if (!empty($article['tags'])): ?>
                    <div class="post-tags">
                        <?php foreach (explode(',', $article['tags']) as $tag): ?>
                            <span class="post-tag">#<?= trim($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="post-stats">
                    <span class="reactions-count">👍 ❤️ 😮 <?= rand(5, 50) ?></span>
                    <span class="comments-count"><?= rand(0, 20) ?> commentaires</span>
                </div>
                
                <div class="article-actions">
                    <button class="article-action-btn" onclick="toggleReactionsPanel<?= $article['id'] ?>()">
                        <span>👍</span> J'aime
                    </button>
                    <button class="article-action-btn" onclick="openCommentsModal<?= $article['id'] ?>()">
                        <span>💬</span> Commenter
                    </button>
                    <button class="article-action-btn" onclick="shareArticle<?= $article['id'] ?>()">
                        <span>📤</span> Partager
                    </button>
                </div>
                
                <!-- Reactions Panel -->
                <div id="reactions-panel-<?= $article['id'] ?>" class="reactions-panel" style="display: none; margin: 10px 20px; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; text-align: center; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);">
                    <p style="margin-bottom: 12px; font-weight: 600; color: white; font-size: 14px;">✨ Choisissez votre réaction</p>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('👍', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">👍</button>
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('❤️', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">❤️</button>
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('😮', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">😮</button>
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('😄', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">😄</button>
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('🔥', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">🔥</button>
                        <button class="emoji-reaction-btn" onclick="sendQuickReaction<?= $article['id'] ?>('👏', event)" style="font-size: 24px; padding: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.95); border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">👏</button>
                    </div>
                    <div id="reaction-stats-<?= $article['id'] ?>" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 13px; color: white; font-weight: 500;"></div>
                </div>
                
                <!-- Share Panel -->
                <div id="share-panel-<?= $article['id'] ?>" class="share-panel" style="display: none; margin: 10px 20px; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; text-align: center; box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);">
                    <p style="margin-bottom: 12px; font-weight: 600; color: white; font-size: 14px;">📣 Partager cet article</p>
                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                        <button onclick="shareOnPlatform<?= $article['id'] ?>('facebook')" style="font-size: 20px; padding: 10px; border: 2px solid rgba(255,255,255,0.3); background: #1877f2; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 900; font-family: Arial, sans-serif;">f</button>
                        <button onclick="shareOnPlatform<?= $article['id'] ?>('twitter')" style="font-size: 20px; padding: 10px; border: 2px solid rgba(255,255,255,0.3); background: #000000; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">𝕏</button>
                        <button onclick="shareOnPlatform<?= $article['id'] ?>('linkedin')" style="font-size: 16px; padding: 10px; border: 2px solid rgba(255,255,255,0.3); background: #0077b5; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-family: Arial, sans-serif;">in</button>
                        <button onclick="shareOnPlatform<?= $article['id'] ?>('whatsapp')" style="font-size: 26px; padding: 10px; border: 2px solid rgba(255,255,255,0.3); background: #25D366; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white;">💬</button>
                        <button onclick="shareOnPlatform<?= $article['id'] ?>('copy')" style="font-size: 20px; padding: 10px; border: 2px solid rgba(255,255,255,0.3); background: #546e7a; border-radius: 50%; cursor: pointer; transition: all 0.3s; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; color: white;">📄</button>
                    </div>
                    <div id="share-feedback-<?= $article['id'] ?>" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 13px; color: white; font-weight: 500;"></div>
                </div>
                
                <!-- Comment Form Section -->
                <div class="comments-section" id="comments-<?= $article['id'] ?>" style="display: none;">
                    <div id="comment-form-<?= $article['id'] ?>" style="margin-bottom: 20px;">
                        <textarea id="comment-message-<?= $article['id'] ?>" required placeholder="Écrivez votre commentaire..." style="width: 100%; padding: 12px; background: #121212; border: 1px solid #3a3a3a; border-radius: 8px; color: #e4e6eb; font-family: 'Rajdhani', sans-serif; font-size: 14px; min-height: 80px; resize: vertical; box-sizing: border-box; margin-bottom: 10px;"></textarea>
                        
                        <button onclick="submitComment<?= $article['id'] ?>()" style="width: 100%; padding: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; font-family: 'Rajdhani', sans-serif; transition: all 0.3s;">Publier le commentaire</button>
                        
                        <div id="comment-feedback-<?= $article['id'] ?>" style="margin-top: 10px; padding: 10px; border-radius: 8px; display: none; font-size: 13px; font-weight: 500;"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // Toggle reactions panel for specific article
        function createToggleReactionsPanel(articleId) {
            return function() {
                const panel = document.getElementById('reactions-panel-' + articleId);
                const sharePanel = document.getElementById('share-panel-' + articleId);
                if (panel.style.display === 'none') {
                    panel.style.display = 'block';
                    panel.classList.add('active');
                    if (sharePanel) {
                        sharePanel.style.display = 'none';
                        sharePanel.classList.remove('active');
                    }
                    loadReactionStats(articleId);
                } else {
                    panel.style.display = 'none';
                    panel.classList.remove('active');
                }
            };
        }
        
        // Send reaction for specific article
        function createSendQuickReaction(articleId) {
            return async function(emoji, e) {
                if (!articleId) {
                    alert('Erreur: Article non trouvé');
                    return;
                }
                
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
                    
                    if (response.ok) {
                        if (e && e.target) {
                            e.target.style.transform = 'scale(1.2)';
                            setTimeout(() => {
                                e.target.style.transform = 'scale(1)';
                            }, 200);
                        }
                        
                        const statsDiv = document.getElementById('reaction-stats-' + articleId);
                        if (statsDiv) {
                            statsDiv.innerHTML = '<span style="color: #4caf50;">✅ Réaction ajoutée!</span>';
                            setTimeout(() => {
                                loadReactionStats(articleId);
                            }, 1000);
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            };
        }
        
        // Load reaction stats for specific article
        async function loadReactionStats(articleId) {
            const statsDiv = document.getElementById('reaction-stats-' + articleId);
            if (!statsDiv) return;
            
            try {
                const response = await fetch(window.location.origin + (window.SITE_BASE || '') + '/blog/index.php?controller=interaction&action=getStats&article_id=' + articleId);
                const text = await response.text();
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
            }
        }
        
        // Share article
        function createShareArticle(articleId) {
            return function() {
                const panel = document.getElementById('share-panel-' + articleId);
                const reactionsPanel = document.getElementById('reactions-panel-' + articleId);
                if (panel.style.display === 'none') {
                    panel.style.display = 'block';
                    panel.classList.add('active');
                    if (reactionsPanel) {
                        reactionsPanel.style.display = 'none';
                        reactionsPanel.classList.remove('active');
                    }
                } else {
                    panel.style.display = 'none';
                    panel.classList.remove('active');
                }
            };
        }
        
        // Share on platform
        function createShareOnPlatform(articleId) {
            return function(platform) {
                const url = 'index.php?controller=article&action=show&id=' + articleId;
                const fullUrl = window.location.origin + window.location.pathname.replace(/[^/]+$/, '') + url;
                const title = document.querySelector('#post-' + articleId + ' .post-title').textContent;
                const feedbackDiv = document.getElementById('share-feedback-' + articleId);
                
                let shareUrl = '';
                
                switch(platform) {
                    case 'facebook':
                        shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(fullUrl);
                        window.open(shareUrl, '_blank', 'width=600,height=400');
                        feedbackDiv.innerHTML = '<span style="color: #4caf50;">📘 Facebook...</span>';
                        break;
                    case 'twitter':
                        shareUrl = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(fullUrl) + '&text=' + encodeURIComponent(title);
                        window.open(shareUrl, '_blank', 'width=600,height=400');
                        feedbackDiv.innerHTML = '<span style="color: #4caf50;">𝕏 Twitter...</span>';
                        break;
                    case 'linkedin':
                        shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(fullUrl);
                        window.open(shareUrl, '_blank', 'width=600,height=400');
                        feedbackDiv.innerHTML = '<span style="color: #4caf50;">💼 LinkedIn...</span>';
                        break;
                    case 'whatsapp':
                        shareUrl = 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + fullUrl);
                        window.open(shareUrl, '_blank');
                        feedbackDiv.innerHTML = '<span style="color: #4caf50;">📱 WhatsApp...</span>';
                        break;
                    case 'copy':
                        navigator.clipboard.writeText(fullUrl).then(() => {
                            feedbackDiv.innerHTML = '<span style="color: #4caf50;">✅ Lien copié!</span>';
                            setTimeout(() => {
                                feedbackDiv.innerHTML = '';
                            }, 3000);
                        }).catch(() => {
                            feedbackDiv.innerHTML = '<span style="color: #ff6b6b;">❌ Erreur</span>';
                        });
                        break;
                }
                
                if (platform !== 'copy') {
                    setTimeout(() => {
                        feedbackDiv.innerHTML = '';
                    }, 3000);
                }
            };
        }
        
        // Toggle comments form
        function createOpenCommentsModal(articleId) {
            return function() {
                const commentsSection = document.getElementById('comments-' + articleId);
                const reactionsPanel = document.getElementById('reactions-panel-' + articleId);
                const sharePanel = document.getElementById('share-panel-' + articleId);
                
                if (commentsSection.style.display === 'none') {
                    commentsSection.style.display = 'block';
                    // Close other panels
                    if (reactionsPanel) {
                        reactionsPanel.style.display = 'none';
                        reactionsPanel.classList.remove('active');
                    }
                    if (sharePanel) {
                        sharePanel.style.display = 'none';
                        sharePanel.classList.remove('active');
                    }
                } else {
                    commentsSection.style.display = 'none';
                }
            };
        }
        
        // Submit comment via AJAX
        function createSubmitComment(articleId) {
            return async function() {
                const messageTextarea = document.getElementById('comment-message-' + articleId);
                const feedbackDiv = document.getElementById('comment-feedback-' + articleId);
                const message = messageTextarea.value.trim();
                
                if (!message) {
                    feedbackDiv.style.display = 'block';
                    feedbackDiv.style.background = '#ff6b6b';
                    feedbackDiv.style.color = 'white';
                    feedbackDiv.textContent = '❌ Veuillez écrire un commentaire';
                    return;
                }
                
                try {
                    const formData = new FormData();
                    formData.append('article_id', articleId);
                    formData.append('type', 'comment');
                    formData.append('auteur', '<?= $_SESSION['user_name'] ?? 'Utilisateur' ?>');
                    formData.append('email', '<?= $_SESSION['user_email'] ?? 'user@example.com' ?>');
                    formData.append('message', message);
                    
                    const response = await fetch(window.location.origin + (window.SITE_BASE || '') + '/blog/index.php?controller=interaction&action=create', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        feedbackDiv.style.display = 'block';
                        feedbackDiv.style.background = '#4caf50';
                        feedbackDiv.style.color = 'white';
                        feedbackDiv.textContent = '✅ Commentaire publié avec succès!';
                        messageTextarea.value = '';
                        
                        setTimeout(() => {
                            feedbackDiv.style.display = 'none';
                            // Optionally close the comment section
                            // document.getElementById('comments-' + articleId).style.display = 'none';
                        }, 3000);
                    } else {
                        throw new Error('Erreur lors de la publication');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    feedbackDiv.style.display = 'block';
                    feedbackDiv.style.background = '#ff6b6b';
                    feedbackDiv.style.color = 'white';
                    feedbackDiv.textContent = '❌ Erreur lors de la publication';
                }
            };
        }
        
        // Initialize functions for each article
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                window['toggleReactionsPanel<?= $article['id'] ?>'] = createToggleReactionsPanel(<?= $article['id'] ?>);
                window['sendQuickReaction<?= $article['id'] ?>'] = createSendQuickReaction(<?= $article['id'] ?>);
                window['shareArticle<?= $article['id'] ?>'] = createShareArticle(<?= $article['id'] ?>);
                window['shareOnPlatform<?= $article['id'] ?>'] = createShareOnPlatform(<?= $article['id'] ?>);
                window['openCommentsModal<?= $article['id'] ?>'] = createOpenCommentsModal(<?= $article['id'] ?>);
                window['submitComment<?= $article['id'] ?>'] = createSubmitComment(<?= $article['id'] ?>);
            <?php endforeach; ?>
        <?php endif; ?>
        
        function toggleReaction(button) {
            button.classList.toggle('active');
            if (button.classList.contains('active')) {
                button.style.color = '#00ff88';
            } else {
                button.style.color = '#b0b3b8';
            }
        }
        
        function toggleComments(articleId) {
            const commentsSection = document.getElementById('comments-' + articleId);
            commentsSection.classList.toggle('show');
        }
        
        function openCreateModal() {
            document.getElementById('createArticleModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        
        function closeCreateModal() {
            document.getElementById('createArticleModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            document.getElementById('createArticleForm').reset();
        }
        
        function selectCategory(category) {
            document.getElementById('categorie').value = category;
            // Update visual state of selected tag
            document.querySelectorAll('.category-tag').forEach(tag => {
                tag.classList.remove('selected');
            });
            event.target.classList.add('selected');
        }
        
        // Close modal on outside click
        document.getElementById('createArticleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
            }
        });
    </script>
</body>
</html>
