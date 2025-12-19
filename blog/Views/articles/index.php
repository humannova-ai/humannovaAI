<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prism Flux | Gestion des Articles</title>
    
    <!-- Google Fonts pour Orbitron et Rajdhani -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lien vers ton style Prism Flux futuriste -->
    <link rel="stylesheet" href="./templatemo-prism-flux.css">
    
    <!-- Style spécifique pour la gestion des articles -->
    <style>
        /* ============================================
           GESTION DES ARTICLES - Style Prism Flux
           ============================================ */
        
        /* Reset et structure */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Orbitron', 'Rajdhani', sans-serif;
            background: #0a0a0a;
            color: #ffffff37;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Arrière-plan carbon fiber */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                repeating-linear-gradient(90deg,
                    transparent,
                    transparent 2px,
                    rgba(255, 255, 255, 0.03) 2px,
                    rgba(255, 255, 255, 0.03) 4px),
                linear-gradient(135deg,
                    #0a0a0a 0%,
                    #121212 25%,
                    #1a1a1a 50%,
                    #121212 75%,
                    #0a0a0a 100%);
            z-index: -2;
        }
        
        /* Grille animée */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 51, 51, 0.05) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255, 51, 51, 0.05) 2px, transparent 2px);
            background-size: 150px 150px;
            animation: gridMove 20s linear infinite;
            z-index: -1;
            opacity: 0.5;
        }
        
        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(150px, 150px); }
        }
        
        /* Container principal */
        .prism-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 30px 50px;
            position: relative;
            z-index: 1;
        }
        
        /* Header de page */
        .page-header {
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 2px solid #3a3a3a;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, #9945ff, transparent);
        }
        
        h1 {
            font-size: 48px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #ffffff, #9945ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Bouton Créer */
        .create-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 15px 35px;
            background: linear-gradient(135deg, #9945ff, #00a8ff);
            border: none;
            border-radius: 30px;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 5px 20px rgba(153, 69, 255, 0.3);
            font-family: 'Orbitron', sans-serif;
            font-size: 14px;
        }
        
        .create-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(153, 69, 255, 0.5);
        }
        
        .create-btn::before {
            content: '⚡';
            font-size: 18px;
        }
        
        /* Tableau futuriste */
        .prism-table-container {
            background: linear-gradient(135deg,
                rgba(42, 42, 42, 0.3),
                rgba(26, 26, 26, 0.5));
            border: 1px solid #3a3a3a;
            border-radius: 20px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: #121212;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #3a3a3a;
        }
        
        thead {
            background: linear-gradient(135deg, #2a2a2a, #1a1a1a);
            border-bottom: 2px solid #3a3a3a;
        }
        
        th {
            padding: 25px 20px;
            text-align: left;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            font-weight: 700;
            border-right: 1px solid #3a3a3a;
            font-family: 'Orbitron', sans-serif;
        }
        
        th:last-child {
            border-right: none;
        }
        
        tr {
            border-bottom: 1px solid #3a3a3a;
            transition: all 0.3s ease;
            background: #121212;
        }
        
        tr:hover {
            background: rgba(153, 69, 255, 0.05);
            transform: translateX(5px);
        }
        
        td {
            padding: 25px 20px;
            border-right: 1px solid #3a3a3a;
        }
        
        td:last-child {
            border-right: none;
        }
        
        /* Style des cellules */
        .id-cell {
            font-family: 'Orbitron', monospace;
            font-size: 18px;
            font-weight: 700;
            color: #00ffff;
        }
        
        .title-cell {
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
        }
        
        .date-cell {
            font-family: 'Orbitron', monospace;
            font-size: 16px;
            color: #b0b0b0;
        }
        
        /* Boutons d'action */
        .actions-container {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            font-family: 'Orbitron', sans-serif;
        }
        
        .view-btn {
            background: rgba(0, 255, 136, 0.1);
            color: #00ff88;
            border-color: #00ff88;
        }
        
        .edit-btn {
            background: rgba(0, 168, 255, 0.1);
            color: #00a8ff;
            border-color: #00a8ff;
        }
        
        .delete-btn {
            background: rgba(255, 51, 51, 0.1);
            color: #ff3333;
            border-color: #ff3333;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .view-btn:hover {
            background: #00ff88;
            color: #0a0a0a;
        }
        
        .edit-btn:hover {
            background: #00a8ff;
            color: #ffffff;
        }
        
        .delete-btn:hover {
            background: #ff3333;
            color: #ffffff;
        }
        
        /* Séparateurs */
        .separator {
            color: #4a4a4a;
            font-weight: 900;
        }
        
        /* Message info */
        .info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding: 20px 30px;
            background: #1a1a1a;
            border-radius: 15px;
            border: 1px solid #3a3a3a;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .info-label {
            font-size: 14px;
            color: #b0b0b0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-value {
            font-size: 24px;
            font-weight: 900;
            color: #ffffff;
            font-family: 'Orbitron', monospace;
        }
        
        /* Footer */
        .prism-footer {
            margin-top: 60px;
            padding: 30px;
            text-align: center;
            color: #808080;
            font-size: 14px;
            border-top: 1px solid #3a3a3a;
        }
        
        .prism-footer .highlight {
            color: #00ffff;
            font-weight: 700;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .prism-container {
                padding: 80px 15px 30px;
            }
            
            h1 {
                font-size: 32px;
            }
            
            .create-btn {
                padding: 12px 25px;
                font-size: 12px;
            }
            
            .prism-table-container {
                padding: 20px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 15px 10px;
                font-size: 12px;
            }
            
            .actions-container {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-btn {
                padding: 8px 15px;
                font-size: 12px;
                justify-content: center;
            }
        }
        
        /* Animation d'entrée */
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
        
        .prism-table-container {
            animation: fadeIn 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="prism-container">
        <!-- Header -->
        <div class="page-header">
            <h1>Gestion des Articles</h1>
            <a href="index.php?controller=article&action=create" class="create-btn">
                Créer un Nouvel Article
            </a>
        </div>
        
        <!-- Tableau des articles -->
        <div class="prism-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td class="id-cell"><?= $a['id'] ?></td>
                        <td class="title-cell"><?= htmlspecialchars($a['titre']) ?></td>
                        <td class="date-cell"><?= $a['date_creation'] ?></td>
                        <td>
                            <div class="actions-container">
                                <a href="index.php?controller=article&action=show&id=<?= $a['id'] ?>" class="action-btn view-btn">
                                    <span>👁️</span> Voir
                                </a>
                                <span class="separator">|</span>
                                <a href="index.php?controller=article&action=edit&id=<?= $a['id'] ?>" class="action-btn edit-btn">
                                    <span>✏️</span> Éditer
                                </a>
                                <span class="separator">|</span>
                                <a href="index.php?controller=article&action=delete&id=<?= $a['id'] ?>" 
                                   class="action-btn delete-btn" 
                                   onclick="return confirm('Supprimer cet article ?')">
                                    <span>🗑️</span> Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Barre d'information -->
        <div class="info-bar">
            <div class="info-item">
                <span class="info-label">Articles Totaux :</span>
                <span class="info-value"><?= count($articles) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Dernière Mise à Jour :</span>
                <span class="info-value"><?= date('H:i:s') ?></span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="prism-footer">
            Système de Gestion de Contenu Prism Flux | 
            Contrôleur: <span class="highlight">article</span> | 
            Action: <span class="highlight">index</span>
        </div>
    </div>
    
    <!-- Script pour les effets interactifs -->
    <script>
        // Effet de survol amélioré pour les lignes
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                });
            });
            
            // Effet de confirmation pour suppression
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Animation d'entrée pour les éléments
            const animateOnScroll = () => {
                const elements = document.querySelectorAll('.prism-table-container, .info-bar');
                
                elements.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    const isVisible = rect.top <= window.innerHeight - 50;
                    
                    if (isVisible) {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }
                });
            };
            
            // Initialiser les styles d'animation
            const animatedElements = document.querySelectorAll('.prism-table-container, .info-bar');
            animatedElements.forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            });
            
            // Démarrer l'animation
            setTimeout(animateOnScroll, 100);
            window.addEventListener('scroll', animateOnScroll);
        });
    </script>
</body>
</html>