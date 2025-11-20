<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Human Nova AI - Gestion d'Événements</title>
    <link rel="stylesheet" href="assets/css/templatemo-prism-flux.css">
    <style>
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        
        .hero-content {
            max-width: 800px;
        }
        
        .hero-title {
            font-size: 64px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 30px;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 3s ease-in-out infinite;
        }
        
        .hero-subtitle {
            font-size: 24px;
            color: var(--text-secondary);
            margin-bottom: 50px;
            line-height: 1.6;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-button {
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
            padding: 18px 45px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 255, 136, 0.5);
            color: #fff;
        }
        
        .cta-button.secondary {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-red));
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 40px;
            }
            
            .hero-subtitle {
                font-size: 18px;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-button {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">
                <span class="logo-text">
                    <span class="prism">HUMAN</span>
                    <span class="flux">NOVA AI</span>
                </span>
            </a>
            <ul class="nav-menu">
                <li><a href="views/front/events.php" class="nav-link">Événements</a></li>
                <li><a href="views/admin/manage-events.php" class="nav-link">Administration</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Human Nova AI</h1>
            <p class="hero-subtitle">
                Plateforme complète de gestion d'événements avec système de quiz interactif.
                Créez, gérez et partagez vos événements en toute simplicité.
            </p>
            <div class="cta-buttons">
                <a href="views/front/events.php" class="cta-button">
                    Voir les événements
                </a>
                <a href="views/admin/manage-events.php" class="cta-button secondary">
                    Administration
                </a>
            </div>
        </div>
    </section>
</body>
</html>