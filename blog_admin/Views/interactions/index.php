<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Interactions | Blog Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 20px;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .admin-header {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #00ff88, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back {
            background: transparent;
            border: 1px solid #3a3a3a;
            color: #8a8d91;
        }
        
        .btn-back:hover {
            border-color: #00ff88;
            color: #00ff88;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
        }
        
        .stat-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 48px;
            font-weight: 700;
            color: #00ff88;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #8a8d91;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #3a3a3a;
            padding-bottom: 10px;
        }
        
        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            color: #8a8d91;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .tab.active {
            color: #00ff88;
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #00ff88;
        }
        
        .tab:hover {
            color: #00ff88;
        }
        
        .interactions-table {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 12px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #2a2a2a;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #00ff88;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        td {
            padding: 15px;
            border-top: 1px solid #3a3a3a;
            font-size: 14px;
        }
        
        tbody tr {
            transition: background 0.2s ease;
        }
        
        tbody tr:hover {
            background: #2a2a2a;
        }
        
        .article-title {
            color: #00ccff;
            font-weight: 600;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .comment-content {
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #b0b3b8;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff88, #00ccff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #0a0a0a;
        }
        
        .date {
            color: #8a8d91;
            font-size: 12px;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            padding: 6px 12px;
            border: 1px solid #3a3a3a;
            border-radius: 6px;
            background: transparent;
            color: #8a8d91;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            border-color: #00ff88;
            color: #00ff88;
        }
        
        .btn-delete:hover {
            border-color: #ff3333;
            color: #ff3333;
        }
        
        .reaction-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            background: #2a2a2a;
            border-radius: 12px;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #8a8d91;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 5px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="header-title">💬 Gestion des Interactions</h1>
            <div class="header-actions">
                <a href="index.php" class="btn btn-back">
                    <span>←</span>
                    Retour au dashboard
                </a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <span class="stat-value"><?= count($interactions ?? []) ?></span>
                <span class="stat-label">Total Commentaires</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">❤️</div>
                <span class="stat-value"><?= count($reactions ?? []) ?></span>
                <span class="stat-label">Total Réactions</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <span class="stat-value"><?= count($interactions ?? []) + count($reactions ?? []) ?></span>
                <span class="stat-label">Total Interactions</span>
            </div>
        </div>
        
        <div class="tabs">
            <button class="tab active" onclick="switchTab('comments')">Commentaires</button>
            <button class="tab" onclick="switchTab('reactions')">Réactions</button>
        </div>
        
        <!-- Commentaires Tab -->
        <div id="comments-tab" class="tab-content active">
            <div class="interactions-table">
                <?php if (empty($interactions)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">💬</div>
                        <p>Aucun commentaire pour le moment</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Article</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($interactions as $interaction): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($interaction['user_id'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span>Utilisateur #<?= $interaction['user_id'] ?? 'N/A' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="article-title" title="<?= htmlspecialchars($interaction['article_titre'] ?? 'Article supprimé') ?>">
                                        <?= htmlspecialchars($interaction['article_titre'] ?? 'Article supprimé') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="comment-content" title="<?= htmlspecialchars($interaction['contenu'] ?? '') ?>">
                                        <?= htmlspecialchars($interaction['contenu'] ?? '') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="date">
                                        <?= date('d/m/Y H:i', strtotime($interaction['date_interaction'] ?? 'now')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action" onclick="viewInteraction(<?= $interaction['id'] ?>)">
                                            👁️ Voir
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteInteraction(<?= $interaction['id'] ?>)">
                                            🗑️ Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Réactions Tab -->
        <div id="reactions-tab" class="tab-content">
            <div class="interactions-table">
                <?php if (empty($reactions)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">❤️</div>
                        <p>Aucune réaction pour le moment</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Article</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reactions as $reaction): ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($reaction['user_id'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span>Utilisateur #<?= $reaction['user_id'] ?? 'N/A' ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="article-title" title="<?= htmlspecialchars($reaction['article_titre'] ?? 'Article supprimé') ?>">
                                        <?= htmlspecialchars($reaction['article_titre'] ?? 'Article supprimé') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="reaction-badge">
                                        <?php
                                        $reactionEmojis = ['like' => '👍', 'love' => '❤️', 'wow' => '😮', 'haha' => '😂', 'sad' => '😢', 'angry' => '😠'];
                                        echo $reactionEmojis[$reaction['type_reaction'] ?? 'like'] ?? '👍';
                                        ?>
                                        <?= ucfirst($reaction['type_reaction'] ?? 'Like') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="date">
                                        <?= date('d/m/Y H:i', strtotime($reaction['date_reaction'] ?? 'now')) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-delete" onclick="deleteReaction(<?= $reaction['id'] ?>)">
                                            🗑️ Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function switchTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update tab content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.getElementById(tab + '-tab').classList.add('active');
        }
        
        function viewInteraction(id) {
            // Implement view interaction details
            alert('Voir le commentaire #' + id);
        }
        
        function deleteInteraction(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                window.location.href = 'index.php?section=interactions&action=delete&type=interaction&id=' + id;
            }
        }
        
        function deleteReaction(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette réaction ?')) {
                window.location.href = 'index.php?section=interactions&action=delete&type=reaction&id=' + id;
            }
        }
    </script>
</body>
</html>
