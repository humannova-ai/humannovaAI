<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin Dashboard</title>
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
            padding: 40px 20px;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 60px;
            padding-bottom: 30px;
            border-bottom: 2px solid #3a3a3a;
        }
        
        .dashboard-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, #00ff88, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        
        .dashboard-subtitle {
            font-size: 18px;
            color: #8a8d91;
            font-weight: 400;
        }
        
        .management-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .management-card {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 16px;
            padding: 40px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .management-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #00ff88, #00ccff);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .management-card:hover {
            border-color: #00ff88;
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 255, 136, 0.2);
        }
        
        .management-card:hover::before {
            transform: scaleX(1);
        }
        
        .card-icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
        }
        
        .card-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .card-description {
            font-size: 16px;
            color: #b0b3b8;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .card-stats {
            display: flex;
            gap: 20px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #3a3a3a;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: #00ff88;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #8a8d91;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        
        .card-actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #00ff88, #00ccff);
            color: #0a0a0a;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.4);
        }
        
        .btn-secondary {
            background: transparent;
            border: 1px solid #3a3a3a;
            color: #8a8d91;
        }
        
        .btn-secondary:hover {
            border-color: #00ff88;
            color: #00ff88;
        }
        
        .quick-actions {
            background: #1a1a1a;
            border: 1px solid #3a3a3a;
            border-radius: 16px;
            padding: 30px;
            margin-top: 40px;
        }
        
        .quick-actions-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .action-btn {
            padding: 12px 24px;
            background: #2a2a2a;
            border: 1px solid #3a3a3a;
            border-radius: 8px;
            color: #e4e6eb;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-btn:hover {
            background: #3a3a3a;
            border-color: #00ff88;
            color: #00ff88;
        }
        
        @media (max-width: 768px) {
            .management-grid {
                grid-template-columns: 1fr;
            }
            
            .dashboard-title {
                font-size: 32px;
            }
            
            .card-stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">⚡ Blog Administration</h1>
            <p class="dashboard-subtitle">Gérez votre contenu et vos interactions</p>
        </div>
        
        <div class="management-grid">
            <!-- Gestion des Articles -->
            <div class="management-card" onclick="window.location.href='index.php?section=posts'">
                <span class="card-icon">📝</span>
                <h2 class="card-title">Gestion des Posts</h2>
                <p class="card-description">
                    Créez, modifiez et gérez tous vos articles de blog. Contrôlez le statut de publication et organisez votre contenu.
                </p>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-value" id="totalPosts">0</span>
                        <span class="stat-label">Total Articles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="publishedPosts">0</span>
                        <span class="stat-label">Publiés</span>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="index.php?section=posts" class="btn btn-primary">Gérer les posts</a>
                </div>
            </div>
            
            <!-- Gestion des Interactions -->
            <div class="management-card" onclick="window.location.href='index.php?section=interactions'">
                <span class="card-icon">💬</span>
                <h2 class="card-title">Gestion des Interactions</h2>
                <p class="card-description">
                    Modérez les commentaires et suivez les réactions de votre communauté. Maintenez un environnement sain et engageant.
                </p>
                <div class="card-stats">
                    <div class="stat-item">
                        <span class="stat-value" id="totalComments">0</span>
                        <span class="stat-label">Commentaires</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value" id="totalReactions">0</span>
                        <span class="stat-label">Réactions</span>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="index.php?section=interactions" class="btn btn-primary">Gérer les interactions</a>
                </div>
            </div>
        </div>
        
        <div class="quick-actions">
            <h3 class="quick-actions-title">Actions Rapides</h3>
            <div class="action-buttons">
                <a href="../blog/index.php" class="action-btn">
                    <span>🌐</span>
                    Voir le blog public
                </a>
                <a href="index.php?section=posts&action=create" class="action-btn">
                    <span>➕</span>
                    Créer un article
                </a>
                <a href="../index.php" class="action-btn">
                    <span>🏠</span>
                    Retour au site
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Load statistics (you can populate these with real data from PHP)
        document.addEventListener('DOMContentLoaded', function() {
            // These would come from your backend
            const stats = {
                totalPosts: <?php echo isset($totalPosts) ? $totalPosts : 0; ?>,
                publishedPosts: <?php echo isset($publishedPosts) ? $publishedPosts : 0; ?>,
                totalComments: <?php echo isset($totalComments) ? $totalComments : 0; ?>,
                totalReactions: <?php echo isset($totalReactions) ? $totalReactions : 0; ?>
            };
            
            document.getElementById('totalPosts').textContent = stats.totalPosts;
            document.getElementById('publishedPosts').textContent = stats.publishedPosts;
            document.getElementById('totalComments').textContent = stats.totalComments;
            document.getElementById('totalReactions').textContent = stats.totalReactions;
        });
    </script>
</body>
</html>
